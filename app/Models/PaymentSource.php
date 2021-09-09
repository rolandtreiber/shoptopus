<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentSource extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'name',
        'source_id',
        'exp_month',
        'exp_year',
        'last_four',
        'brand',
        'stripe_user_id',
        'payment_method_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'payment_method_id' => 'integer',
    ];


    public function payments()
    {
        return $this->hasMany(\App\Payment::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }
}
