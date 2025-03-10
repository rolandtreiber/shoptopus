<?php

namespace App\Models;

use App\Helpers\GeneralHelper;
use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable;
use Shoptopus\ExcelImportExport\Exportable;
use Shoptopus\ExcelImportExport\traits\HasExportable;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * @property string $id
 * @property string $user_id
 * @property Collection<Product> $products
 * @property int $total_weight
 * @method static count()
 * @method static find(int $selectedCartId)
 *
 * @property mixed $updated_at
 */
class Cart extends Model implements Auditable, Exportable
{
    use HasFactory, HasUUID, \OwenIt\Auditing\Auditable, HasExportable, HasSlug;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'ip_address',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'string',
        'user_id' => 'string',
    ];

    /**
     * @param VoucherCode|null $voucherCode
     * @return array
     */
    public function getTotals(VoucherCode|null $voucherCode): array
    {
        $products = $this->products;

        // @phpstan-ignore-next-line
        $originalPrice = $products->sum(function (Product $cp) {
            if ($cp->pivot->product_variant_id) {
                return $cp->pivot->quantity * ProductVariant::find($cp->pivot->product_variant_id)->price;
            } else {
                return $cp->pivot->quantity * $cp->price;
            }
        });

        // @phpstan-ignore-next-line
        $finalPrice = $products->sum(function (Product $cp) {
            if ($cp->pivot->product_variant_id) {
                return $cp->pivot->quantity * ProductVariant::find($cp->pivot->product_variant_id)->final_price;
            } else {
                return $cp->pivot->quantity * $cp->final_price;
            }

        });

        if ($voucherCode) {
            $basis = match (config('shoptopus.discount_rules.voucher_code_basis')) {
                'total_price' => $originalPrice,
                'final_price' => $finalPrice,
                default => $originalPrice,
            };
            $totalPrice = GeneralHelper::getDiscountedValue($voucherCode->type, $voucherCode->amount, $basis);
        } else {
            $totalPrice = $finalPrice;
        }
        $totalDiscount = $originalPrice - $totalPrice;

        return [
            'original_price' => $originalPrice,
            'total_price' => $totalPrice,
            'total_discount' => $totalDiscount
        ];
    }

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(['user.name'])
            ->saveSlugsTo('slug');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'cart_product')
            ->view('active')
            ->withPivot(['quantity', 'product_variant_id'])
            ->using(CartProduct::class);
    }

    public static function quantityValidationRule($productId, $productVariantId, $cartId, $quantity): array
    {
        return ['required', 'integer', 'min:1', function ($attribute, $value, $fail) use ($productId, $productVariantId, $quantity, $cartId) {
            if ($productVariantId !== null) {
                $productQuery = DB::table('product_variants')
                    ->whereNull('deleted_at')
                    ->where('id', $productVariantId)
                    ->where('product_id', $productId);
            } else {
                $productQuery = DB::table('products')
                    ->whereNull('deleted_at')
                    ->where('id', $productId);
            }

            if (! $productQuery->exists()) {
                $fail('Product is unavailable.');
            } else {
                $stock = (int) $productQuery->select(['stock'])->first()['stock'];

                $alreadyInCart = DB::table('cart_product')
                    ->where('cart_id', '=', $cartId)
                    ->where('product_id', '=', $productId);
                if ($productVariantId) {
                    $alreadyInCart = $alreadyInCart->where('product_variant_id', '=', $productVariantId);
                }
                $alreadyInCart = $alreadyInCart->first();
                if ($alreadyInCart) {
                    $alreadyInCart = $alreadyInCart['quantity'];
                }
                $desiredValue = $alreadyInCart;
                if ($quantity === -1) {
                    $desiredValue = $alreadyInCart + 1;
                } else {
                    $desiredValue = $quantity;
                }
                if ($alreadyInCart && ($desiredValue > $stock)) {
                    $fail('Cannot add more of this product.');
                }
                if ($stock < $value) {
                    if ($stock === 0) {
                        $fail('Out of stock.');
                    } elseif ($stock === 1) {
                        $fail('Only 1 left.');
                    } else {
                        $fail('Only '.$stock.' left.');
                    }
                }
            }
        }];
    }

    public function getTotalWeightAttribute()
    {
        $products = $this->products;
        // @phpstan-ignore-next-line
        return $products->sum(function (Product $product) {
            return $product->weight * $product->pivot->quantity;
        });
    }
}
