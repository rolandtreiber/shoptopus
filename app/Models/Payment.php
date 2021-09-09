<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

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
        'id' => 'integer',
        'payable_id' => 'integer',
        'payment_source_id' => 'integer',
        'user_id' => 'integer',
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
