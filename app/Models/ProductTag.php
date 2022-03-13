<?php

namespace App\Models;

use App\Traits\HasFile;
use App\Traits\HasUUID;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Support\Facades\Date;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Translatable\HasTranslations;
use Shoptopus\ExcelImportExport\Exportable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Shoptopus\ExcelImportExport\traits\HasExportable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @method static count()
 * @property string $badge
 * @property boolean $display_badge
 * @property boolean $enabled
 * @property Date $updated_at
 */
class ProductTag extends SearchableModel implements Auditable, Exportable
{
    use HasFactory, SoftDeletes, HasTranslations, HasFile, HasUUID, \OwenIt\Auditing\Auditable, HasSlug, HasExportable;

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(['name'])
            ->saveSlugsTo('slug');
    }

    public $translatable = ['name', 'description'];

    /**
     * @var array
     */
    protected $exportableFields = [
        'slug',
        'name',
        'description',
        'display_badge',
        'enabled'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'display_badge',
        'enabled'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'badge' => 'object',
        'display_badge' => 'boolean',
        'enabled' => 'boolean',
    ];

    /**
     * @return BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }
}
