<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryRule extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'delivery_type_id',
        'status',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'delivery_type_id' => 'integer',
        'status' => 'integer',
    ];


    public function deliveryType()
    {
        return $this->belongsTo(\App\DeliveryType::class);
    }

    public function deliveryType()
    {
        return $this->belongsTo(\App\DeliveryType::class);
    }
}
