<?php

namespace App\Models;

use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

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
        'lat',
        'lon',
        'min_weight',
        'max_weight',
        'min_distance',
        'max_distance',
        'status',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'delivery_type_id' => 'string',
        'status' => 'integer',
        'postcodes' => 'array',
        'lat' => 'decimal:6',
        'lon' => 'decimal:6',
        'min_weight' => 'integer',
        'max_weight' => 'integer',
        'min_distance' => 'integer',
        'max_distance' => 'integer',
    ];

    /**
     * @return BelongsTo
     */
    public function delivery_type(): BelongsTo
    {
        return $this->belongsTo(DeliveryType::class);
    }

}
