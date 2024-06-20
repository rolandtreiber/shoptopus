<?php

namespace App\Models;

use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable;
use Shoptopus\ExcelImportExport\Exportable;
use Shoptopus\ExcelImportExport\traits\HasExportable;
use Spatie\Translatable\HasTranslations;

/**
 * @property string $id
 * @property string $cart_id
 * @property string $product_id
 * @property string|null $product_variant_id
 * @property int $quantity
 *
 */
class CartProduct extends MorphPivot implements Exportable, Auditable
{
    use HasTranslations;
    use HasUUID;
    use HasTimestamps;
    use \OwenIt\Auditing\Auditable, HasExportable;

    protected $table = 'cart_product';

    public $translatable = ['name'];

    /**
     * @var string[]
     */
    protected $exportableFields = [
        'quantity',
        'cart_id',
        'product_id',
        'product_variant_id'
    ];

    /**
     * @var string[]
     */
    protected $exportableRelationships = [
        'cart',
        'product',
        'productVariant',
    ];

    protected $appends = ['name', 'final_price', 'price', 'remaining_stock', 'in_other_carts'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'product_id' => 'string',
        'product_variant_id' => 'string',
        'cart_id' => 'string',
    ];

    public function getNameAttribute()
    {
        if ($this->productVariant) {
            return $this->productVariant->name;
        }
        return $this->product->getTranslations('name');
    }

    public function getFinalPriceAttribute()
    {
        if ($this->productVariant) {
            return $this->product->getFinalPriceAttribute($this->productVariant->price);
        }
        return $this->product->final_price;
    }

    public function getPriceAttribute()
    {
        if ($this->productVariant) {
            return $this->productVariant->price;
        }
        return $this->product->price;
    }

    public function getRemainingStockAttribute()
    {
        if ($this->productVariant) {
            $remainingStock = $this->productVariant->stock - $this->quantity;
            if ($remainingStock < 0) {
                DB::table('cart_product')
                    ->where([
                        'cart_id' => $this->cart_id,
                        'product_id' => $this->product_id,
                        'product_variant_id' => $this->product_variant_id
                    ])->update(['quantity' => $this->productVariant->stock]);
                $this->productVariant = $this->productVariant->refresh();
                $remainingStock = 0;
            }
        } else {
            $this->product = $this->product->refresh();
            $remainingStock = $this->product->stock - $this->quantity;
            if ($remainingStock < 0) {
                DB::table('cart_product')
                    ->where([
                        'cart_id' => $this->cart_id,
                        'product_id' => $this->product_id,
                        'product_variant_id' => null
                    ])->update(['quantity' => $this->product->stock]);
                $remainingStock = 0;
            }
        }
        return $remainingStock;
    }

    public function getInOtherCartsAttribute(): int
    {
        if ($this->productVariant !== null) {
            return DB::table('cart_product')->whereNot('cart_id', $this->cart_id)->where('product_variant_id', $this->product_variant_id)->sum('quantity');
        } else {
            return DB::table('cart_product')->whereNot('cart_id', $this->cart_id)->where('product_id', $this->product_id)->sum('quantity');
        }
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
