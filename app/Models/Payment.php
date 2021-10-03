<?php

namespace App\Models;

use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @property string $id
 * @property mixed|string[]|null $proof
 * @property float|mixed $amount
 * @property string $user_id
 * @property mixed|string $payable_type
 * @property mixed|string $payable_id
 * @property int|mixed $status
 * @property int|mixed $type
 * @property mixed|string $description
 * @property string $created_at
 * @property User $user
 */
class Payment extends SearchableModel implements Auditable
{
    use HasFactory;
    use HasUUID;
    use \OwenIt\Auditing\Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'payable_type',
        'payable_id',
        'payment_source_id',
        'user_id',
        'decimal',
        'status',
        'payment_ref',
        'method_ref',
        'proof',
        'type',
        'amount',
        'description',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'payable_id' => 'string',
        'payment_source_id' => 'string',
        'user_id' => 'string',
        'status' => 'integer',
        'type' => 'integer',
        'proof' => 'object'
    ];


    public function paymentSource(): BelongsTo
    {
        return $this->belongsTo(PaymentSource::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
