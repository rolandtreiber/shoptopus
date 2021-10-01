<?php

namespace App\Models;

use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @property mixed|string $delivery_type_id
 * @property string $id
 * @property string|array $postcodes
 * @property int|null $min_weight
 * @property int|null $max_weight
 * @property int|null $min_distance
 * @property int|null $max_distance
 * @property float|null $lat
 * @property float|null $lon
 * @property int $status
 */
class DeliveryRule extends SearchableModel implements Auditable
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
    public function deliveryType(): BelongsTo
    {
        return $this->belongsTo(DeliveryType::class);
    }

}
