<?php

namespace App\Models;

use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountRule extends Model
{
    use HasFactory;
    use HasUUID;

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
        'id' => 'string',
        'discountable_id' => 'string',
        'type' => 'integer',
        'amount' => 'decimal',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
    ];
}
