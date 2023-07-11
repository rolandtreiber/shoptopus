<?php

namespace App\Models;

use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Shoptopus\ExcelImportExport\Exportable;
use Shoptopus\ExcelImportExport\traits\HasExportable;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Address extends Model implements Auditable, Exportable
{
    use HasFactory, SoftDeletes, HasUUID, \OwenIt\Auditing\Auditable, HasSlug, HasExportable;

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(['user.name', 'name', 'town'])
            ->saveSlugsTo('slug');
    }

    /**
     * @var array
     */
    protected $exportableFields = [
        'slug',
        'town',
        'post_code',
        'country',
        'name',
        'address_line_1',
        'address_line_2',
        'lat',
        'lon',
        'google_maps_url',
        'created_at',
        'deleted_at',
    ];

    protected $exportableRelationships = [
        'user',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'address_line_1',
        'town',
        'post_code',
        'country',
        'user_id',
        'name',
        'address_line_2',
        'lat',
        'lon',
        'deleted_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'string',
        'lat' => 'decimal:6',
        'lon' => 'decimal:6',
    ];

    protected $appends = ['google_maps_url'];

    /**
     * An address belongs to a user
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getGoogleMapsUrlAttribute(): ?string
    {
        return $this->lat && $this->lon ? 'https://www.google.com/maps/@'.$this->lat.','.$this->lon.',14z' : null;
    }
}
