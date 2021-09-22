<?php

namespace App\Models;

use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Payment extends Model implements Auditable
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
        'type',
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
    ];


    public function paymentSource()
    {
        return $this->belongsTo(\App\PaymentSource::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }
}
