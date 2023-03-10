<?php

namespace App\Models\Transaction;

use App\Models\Order;
use Illuminate\Database\Eloquent\Model;

class PayPalTransaction extends Model
{
    /**
     * The table
     */
    protected $table = 'transaction_paypal';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'transaction_id',
        'status_code',
        'intent',
        'status',
        'reference_id',
        'charge_amount',
        'currency_code',
        'merchant_id',
        'merchant_email',
        'soft_descriptor',
        'payer_firstname',
        'payer_surname',
        'payer_email',
        'payer_id',
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
