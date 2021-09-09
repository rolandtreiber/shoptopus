<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountRule extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'discountable_type',
        'discountable_id',
        'type',
        'amount',
        'valid_from',
        'valid_until',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'discountable_id' => 'integer',
        'type' => 'integer',
        'amount' => 'decimal',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
    ];
}
