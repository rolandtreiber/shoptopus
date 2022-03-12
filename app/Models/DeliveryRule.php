<?php

namespace App\Models;

use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Shoptopus\ExcelImportExport\Exportable;
use Shoptopus\ExcelImportExport\traits\HasExportable;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

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
class DeliveryRule extends SearchableModel implements Auditable, Exportable
{
    use HasFactory, HasUUID, \OwenIt\Auditing\Auditable, SoftDeletes, HasSlug, HasExportable;

    use HasSlug;
    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(['deliveryType.name'])
            ->saveSlugsTo('slug');
    }

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
        'enabled',
        'deleted_at'
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
