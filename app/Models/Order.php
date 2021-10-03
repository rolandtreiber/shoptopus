<?php

namespace App\Models;

use App\Enums\DiscountTypes;
use App\Helpers\GeneralHelper;
use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\DB;

/**
 * @method static find(int $selectedCartId)
 * @property int|mixed $user_id
 * @property int|mixed $delivery_type_id
 * @property int $status
 * @property mixed $address_id
 * @property VoucherCode|null $voucherCode
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
 * @property Payment[] $payments
 */
class Order extends SearchableModel
{
    use HasFactory, HasUUID;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'status',
        'original_price',
        'total_price'
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
        'original_price' => 'decimal:2',
        'total_price' => 'decimal:2'
    ];

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
        return $this->belongsToMany(Product::class)->withPivot(['name', 'amount', 'full_price', 'unit_price', 'final_price'])->using(OrderProduct::class);
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
    public function voucherCode(): BelongsTo
    {
        return $this->belongsTo(VoucherCode::class);
    }

    /**
     * @return BelongsTo
     */
    public function deliveryRule(): BelongsTo
    {
        return $this->belongsTo(DeliveryRule::class);
    }

    public function recalculatePrices()
    {
        $dispatcher = Order::getEventDispatcher();
        Order::unsetEventDispatcher();
        $finalPrice = DB::table('order_product')->where('order_id', $this->id)->sum('final_price');
        $originalPrice = DB::table('order_product')->where('order_id', $this->id)->sum('full_price');
        $this->original_price = $originalPrice;
        $voucherCode = $this->voucherCode;
        if ($voucherCode) {
            switch (config('shoptopus.voucher_code_basis')) {
                case 'full_price':
                    $basis = $originalPrice;
                    break;
                case 'final_price':
                    $basis = $finalPrice;
                    break;
                default:
                    $basis = $finalPrice;
            }
            $this->total_price = GeneralHelper::getDiscountedValue($voucherCode->type, $voucherCode->amount, $basis);
        } else {
            $this->total_price = $finalPrice;
        }
        $this->save();
        Order::setEventDispatcher($dispatcher);
    }
}
