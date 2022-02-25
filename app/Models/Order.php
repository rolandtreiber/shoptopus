<?php

namespace App\Models;

use App\Traits\HasUUID;
use App\Traits\HasEventLogs;
use App\Enums\OrderStatuses;
use App\Helpers\GeneralHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends SearchableModel
{
    use HasFactory, HasUUID, SoftDeletes, HasEventLogs;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'status',
        'original_price',
        'subtotal',
        'total_price',
        'total_discount',
        'delivery_cost'
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
            case 'paid':
                $query->where('status', OrderStatuses::Paid);
                break;
            case 'processing':
                $query->where('status', OrderStatuses::Processing);
                break;
            case 'in_transit':
                $query->where('status', OrderStatuses::InTransit);
                break;
            case 'completed':
                $query->where('status', OrderStatuses::Completed);
                break;
            case 'on_hold':
                $query->where('status', OrderStatuses::OnHold);
                break;
            case 'cancelled':
                $query->where('status', OrderStatuses::Cancelled);
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
