<?php

namespace App\Models\Transaction;

use App\Models\Order;
use Illuminate\Database\Eloquent\Model;

class StripeTransaction extends Model
{
    /**
     * The table
     */
    protected $table = 'transaction_stripe';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'order_id',
        'payment_id',
        'object',
        'amount',
        'canceled_at',
        'cancellation_reason',
        'capture_method',
        'confirmation_method',
        'created',
        'currency',
        'description',
        'last_payment_error',
        'livemode',
        'next_action',
        'next_source_action',
        'payment_method',
        'payment_method_types',
        'receipt_email',
        'setup_future_usage',
        'shipping',
        'source',
        'status',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * An order item belongs to a transaction
     */
    public function order(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}
