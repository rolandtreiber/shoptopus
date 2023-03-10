<?php

namespace App\Models;

use App\Traits\HasFile;
use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Date;
use OwenIt\Auditing\Contracts\Auditable;
use Shoptopus\ExcelImportExport\Exportable;
use Shoptopus\ExcelImportExport\Importable;
use Shoptopus\ExcelImportExport\traits\HasExportable;
use Shoptopus\ExcelImportExport\traits\HasImportable;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

/**
 * @method static count()
 *
 * @property string $badge
 * @property bool $display_badge
 * @property bool $enabled
 * @property Date $updated_at
 */
class ProductTag extends SearchableModel implements Auditable, Exportable, Importable
{
    use HasFactory, SoftDeletes, HasTranslations, HasFile, HasUUID, \OwenIt\Auditing\Auditable, HasSlug, HasExportable, HasImportable;

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
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
        'enabled',
    ];

    protected $importableFields = [
        'name' => [
            'validation' => ['unique:product_tags,name'],
        ],
        'description',
        'enabled' => [
            'description' => '0 = disabled, 1 = enabled',
            'validation' => 'boolean',
        ],
        'display_badge' => [
            'description' => '0 = disabled, 1 = enabled',
            'validation' => 'boolean',
        ],
    ];

    protected $importableRelationships = [
        'products',
    ];

    protected $exportableRelationships = [
        'products',
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
        'enabled',
        'deleted_at',
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
