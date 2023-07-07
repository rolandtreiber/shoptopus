<?php

namespace App\Models\Transaction;

use App\Models\Order;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $order_id
 * @property string $transaction_id
 * @property integer $status_code
 * @property string $intent
 * @property string $status
 * @property string $reference_id
 * @property string $charge_amount
 * @property string $currency_code
 * @property string $merchant_id
 * @property string $merchant_email
 * @property string $soft_descriptor
 * @property string $payer_firstname
 * @property string $payer_surname
 * @property string $payer_email
 * @property string $payer_id
 */
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
     */
    public function order(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}
