<?php

namespace App\Models\Transaction;

use App\Models\Order;
use Illuminate\Database\Eloquent\Model;

class AmazonTransaction extends Model
{
    /**
     * The table
     */
    protected $table = 'transaction_amazon';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'request_id',
        'checkout_session_id',
        'charge_id',
        'product_type',
        'merchant_reference_id',
        'merchant_store_name',
        'buyer_id',
        'buyer_name',
        'buyer_email',
        'state',
        'reason_code',
        'reason_description',
        'amazon_last_updated_timestamp',
        //       'payment_intent',
        //       'charge_amount',
        //       'currency_code',
        //       'environment'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * An order item belongs to a transaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}
