<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Helpers\GeneralHelper;
use App\Traits\HasEventLogs;
use App\Traits\HasNote;
use App\Traits\HasUUID;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable;
use Shoptopus\ExcelImportExport\Exportable;
use Shoptopus\ExcelImportExport\traits\HasExportable;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * @method static find(int $selectedCartId)
 *
 * @property int|mixed $user_id
 * @property int|mixed $delivery_type_id
 * @property int $status
 * @property mixed $address_id
 * @property VoucherCode|null $voucher_code
 * @property string $id
 * @property string|null $voucher_code_id
 * @property int $originalPrice
 * @property float $original_price
 * @property float $total_price
 * @property float $delivery_cost
 * @property mixed $created_at
 * @property Address|null $address
 * @property User $user
 * @property Invoice|null $invoice
 * @property Collection<Product> $products
 * @property mixed $pivot
 * @property float $delivery
 * @property Payment[] $payments
 * @property Carbon $updated_at
 * @property float $total_discount
 * @property float $subtotal
 */
class Order extends SearchableModel implements Auditable, Exportable
{
    use HasFactory, HasUUID, SoftDeletes, HasEventLogs, \OwenIt\Auditing\Auditable, HasExportable, HasSlug, HasNote;

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(['user.name', 'address.town'])
            ->saveSlugsTo('slug');
    }

    /**
     * @var string[]
     */
    protected $exportableFields = [
        'slug',
        'currency_code',
        'status',
        'original_price',
        'subtotal',
        'total_price',
        'total_discount',
    ];

    /**
     * @var string[]
     */
    protected $exportableRelationships = [
        'user',
        'address',
        'products',
        'payments',
        'voucher_code',
        'delivery_type',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'delivery_type_id',
        'voucher_code_id',
        'address_id',
        'currency_code',
        'status',
        'original_price',
        'subtotal',
        'total_price',
        'total_discount',
        'delivery_cost',
        'deleted_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'string',
        'user_id' => 'string',
        'status' => 'integer',
        'original_price' => 'float',
        'subtotal' => 'float',
        'total_price' => 'float',
        'total_discount' => 'float',
        'delivery_cost' => 'float',
    ];

    public function scopeSearch($query, $search)
    {
        $query->whereHas('user', function ($q) use ($search) {
            $q->where('name', 'like', '%'.$search.'%');
        });
    }

    public function scopeView($query, $view)
    {
        switch ($view) {
            case 'awaiting_payment':
                $query->where('status', OrderStatus::AwaitingPayment);
                break;
            case 'paid':
                $query->where('status', OrderStatus::Paid);
                break;
            case 'processing':
                $query->where('status', OrderStatus::Processing);
                break;
            case 'in_transit':
                $query->where('status', OrderStatus::InTransit);
                break;
            case 'completed':
                $query->where('status', OrderStatus::Completed);
                break;
            case 'on_hold':
                $query->where('status', OrderStatus::OnHold);
                break;
            case 'cancelled':
                $query->where('status', OrderStatus::Cancelled);
                break;
        }
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)->withPivot(['id', 'name', 'amount', 'full_price', 'original_unit_price', 'unit_price', 'final_price', 'unit_discount', 'total_discount', 'product_variant_id', 'urls'])->using(OrderProduct::class);
    }

    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function voucher_code(): BelongsTo
    {
        return $this->belongsTo(VoucherCode::class);
    }

    public function delivery_type_relation(): BelongsTo
    {
        return $this->belongsTo(DeliveryType::class, 'delivery_type_id', 'id');
    }

    public function getDeliveryTypeAttribute(): ?DeliveryType
    {
        // If the delivery type, the order associated with, was deleted, let's take the
        // snapshot version of the delivery type from the invoice.
        // If the name changed, the user should see the updated name.
        // The delivery price change does not affect the orders that have already been placed.
        if (! $this->delivery_type_relation) {
            if ($this->invoice) {
                $deliveryType = new DeliveryType();
                $deliveryType->fill(json_decode(json_encode($this->invoice->delivery_type), true));

                return $deliveryType;
            } else {
                return null;
            }
        } else {
            return $this->delivery_type_relation;
        }
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    public function recalculatePrices()
    {
        $dispatcher = Order::getEventDispatcher();
        Order::unsetEventDispatcher();

        $voucher_code = $this->voucher_code;
        if ($voucher_code) {
            $sumField = match (config('shoptopus.discount_rules.voucher_code_basis')) {
                'total_price' => 'full_price',
                'final_price' => 'final_price',
                default => 'final_price',
            };
            $subTotal = DB::table('order_product')->where('order_id', $this->id)->sum($sumField);
            $originalPrice = DB::table('order_product')->where('order_id', $this->id)->sum('full_price');
            $total = $subTotal + $this->delivery_cost;

            $basis = match (config('shoptopus.discount_rules.voucher_code_basis')) {
                'total_price' => $originalPrice,
                'final_price' => $subTotal,
                default => $subTotal,
            };

            $this->total_price = GeneralHelper::getDiscountedValue($voucher_code->type, $voucher_code->amount, $basis) + $this->delivery_cost;
        } else {
            $subTotal = DB::table('order_product')->where('order_id', $this->id)->sum('final_price');
            $originalPrice = DB::table('order_product')->where('order_id', $this->id)->sum('full_price');
            $total = $subTotal + $this->delivery_cost;
            $this->total_price = $total;
        }
        $this->subtotal = $subTotal;
        $this->original_price = $originalPrice;
        $this->total_discount = $this->original_price + $this->delivery_cost - $this->total_price;
        $this->save();
        Order::setEventDispatcher($dispatcher);
    }

    public function hasVirtualProduct()
    {
        return $this->products->where('virtual', true)->count() > 0;
    }

    public function hasOnlyVirtualProducts()
    {
        return $this->products->where('virtual', false)->count() === 0 && $this->products->where('virtual', true)->count() > 0;
    }

}
