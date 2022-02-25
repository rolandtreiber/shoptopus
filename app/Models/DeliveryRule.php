<?php

namespace App\Models;

use App\Traits\HasUUID;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeliveryRule extends SearchableModel implements Auditable
{
    use HasFactory, HasUUID, \OwenIt\Auditing\Auditable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'delivery_type_id',
        'postcodes',
        'min_weight',
        'max_weight',
        'min_distance',
        'max_distance',
        'distance_unit',
        'lat',
        'lon',
        'enabled'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'delivery_type_id' => 'string',
        'postcodes' => 'array',
        'min_weight' => 'integer',
        'max_weight' => 'integer',
        'min_distance' => 'float',
        'max_distance' => 'float',
        'lat' => 'decimal:6',
        'lon' => 'decimal:6',
        'enabled' => 'boolean'
    ];

    /**
     * @return BelongsTo
     */
    public function delivery_type(): BelongsTo
    {
        return $this->belongsTo(DeliveryType::class);
    }

}
