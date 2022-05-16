<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\HasUUID;
use App\Enums\OrderStatus;
use App\Traits\HasEventLogs;
use App\Helpers\GeneralHelper;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable;
use Shoptopus\ExcelImportExport\Exportable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Shoptopus\ExcelImportExport\traits\HasExportable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
//use Spatie\Sluggable\HasSlug;
//use Spatie\Sluggable\SlugOptions;

/**
 * @method static find(int $selectedCartId)
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
 * @property mixed $created_at
 * @property Address $address
 * @property User $user
 * @property Product[] $products
 * @property mixed $pivot
 * @property float $delivery
 * @property Payment[] $payments
 * @property Carbon $updated_at
 * @property float $total_discount
 * @property float $subtotal
 */
class Order extends SearchableModel implements Auditable, Exportable
{
    use HasFactory, HasUUID, SoftDeletes, HasEventLogs, \OwenIt\Auditing\Auditable, HasExportable;

//    /**
//     * Get the options for generating the slug.
//     */
//    public function getSlugOptions() : SlugOptions
//    {
//        return SlugOptions::create()
//            ->generateSlugsFrom(['user.name', 'address.town'])
//            ->saveSlugsTo('slug');
//    }

    /**
     * @var string[]
     */
    protected $exportableFields = [
//        'slug',
        'currency_code',
        'status',
        'original_price',
        'subtotal',
        'total_price',
        'total_discount'
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
        'delivery_type'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
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
        'deleted_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'user_id' => 'string',
        'status' => 'integer',
        'original_price' => 'float',
        'subtotal' => 'float',
        'total_price' => 'float',
        'total_discount' => 'float',
        'delivery_cost' => 'float'
    ];

    public function scopeSearch($query, $search)
    {
        $query->whereHas('user', function($q) use($search) {
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

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * @return BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)->withPivot(['id', 'name', 'amount', 'full_price', 'original_unit_price', 'unit_price', 'final_price', 'unit_discount', 'total_discount'])->using(OrderProduct::class);
    }

    /**
     * @return MorphMany
     */
    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    /**
     * @return BelongsTo
     */
    public function voucher_code(): BelongsTo
    {
        return $this->belongsTo(VoucherCode::class);
    }

    /**
     * @return BelongsTo
     */
    public function delivery_type(): BelongsTo
    {
        return $this->belongsTo(DeliveryType::class);
    }

    public function recalculatePrices()
    {
        $dispatcher = Order::getEventDispatcher();
        Order::unsetEventDispatcher();

        $subTotal = DB::table('order_product')->where('order_id', $this->id)->sum('final_price');
        $originalPrice = DB::table('order_product')->where('order_id', $this->id)->sum('full_price');
        $discount = DB::table('order_product')->where('order_id', $this->id)->sum('total_discount');
        $total = $subTotal + $this->delivery_cost;

        $this->total_discount = $discount;
        $this->subtotal = $subTotal;
        $this->original_price = $originalPrice;
        $voucher_code = $this->voucher_code;
        if ($voucher_code) {
            $basis = match (config('shoptopus.voucher_code_basis'))
            {
                'full_price' => $originalPrice,
                'final_price' => $total,
                default => $total,
            };
            $this->total_price = GeneralHelper::getDiscountedValue($voucher_code->type, $voucher_code->amount, $basis);
        } else {
            $this->total_price = $total;
        }
        $this->save();
        Order::setEventDispatcher($dispatcher);
    }
}
