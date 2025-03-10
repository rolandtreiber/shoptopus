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
 * @property string|array $countries
 * @property int|null $min_weight
 * @property int|null $max_weight
 * @property int|null $min_distance
 * @property int|null $max_distance
 * @property string $distance_unit
 * @property boolean $enabled
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
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(['delivery_type.name'])
            ->saveSlugsTo('slug');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'delivery_type_id',
        'postcodes',
        'countries',
        'min_weight',
        'max_weight',
        'min_distance',
        'max_distance',
        'distance_unit',
        'lat',
        'lon',
        'enabled',
        'deleted_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'string',
        'delivery_type_id' => 'string',
        'postcodes' => 'array',
        'countries' => 'array',
        'min_weight' => 'integer',
        'max_weight' => 'integer',
        'min_distance' => 'float',
        'max_distance' => 'float',
        'lat' => 'decimal:6',
        'lon' => 'decimal:6',
        'enabled' => 'boolean',
    ];

    /**
     * @var string[]
     */
    protected $exportableFields = [
        'slug',
        'postcodes',
        'countries',
        'min_weight',
        'max_weight',
        'min_distance',
        'max_distance',
        'distance_unit',
        'lat',
        'lon',
        'enabled',
    ];

    /**
     * @var string[]
     */
    protected $exportableRelationships = [
        'delivery_type',
    ];

    public function delivery_type(): BelongsTo
    {
        return $this->belongsTo(DeliveryType::class);
    }

    /**
     * Calculates the great-circle distance between two points, with
     * the Vincenty formula.
     * @param Address $address
     * @return float|int Distance between points in [m] (same as earthRadius)
     */
    public function getDistanceFromAddress(Address $address): float|int
    {
        $earthRadius = 6371000;
        $latitudeFrom = $address->lat;
        $longitudeFrom = $address->lon;

        $latitudeTo = $this->lat;
        $longitudeTo = $this->lon;
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $lonDelta = $lonTo - $lonFrom;
        $a = pow(cos($latTo) * sin($lonDelta), 2) +
            pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
        $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

        $angle = atan2(sqrt($a), $b);
        return $angle * $earthRadius;
    }
}
