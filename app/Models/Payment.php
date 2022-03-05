<?php

namespace App\Models;

use App\Traits\HasUUID;
use App\Enums\PaymentTypes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Payment extends SearchableModel implements Auditable
{
    use HasFactory, HasUUID, \OwenIt\Auditing\Auditable, SoftDeletes;

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

    public function scopeView($query, $view)
    {
        switch ($view) {
            case 'payment':
                $query->where('type', PaymentTypes::Payment);
                break;
            case 'refund':
                $query->where('type', PaymentTypes::Refund);
        }
    }

    public function paymentSource(): BelongsTo
    {
        return $this->belongsTo(PaymentSource::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
