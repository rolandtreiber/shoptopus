<?php

namespace App\Models;

use App\Traits\HasFile;
use App\Traits\HasUUID;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;
use OwenIt\Auditing\Contracts\Auditable;
use Shoptopus\ExcelImportExport\Exportable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Shoptopus\ExcelImportExport\traits\HasExportable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property mixed $image
 * @property mixed $id
 * @property mixed $type
 * @property mixed $product_attribute_id
 * @property mixed $value
 * @mixin SearchableModel
 */
class ProductAttributeOption extends SearchableModel implements Auditable, Exportable
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

    public $translatable = ['name'];

    /**
     * @var array
     */
    protected $exportableFields = [
        'slug',
        'name',
        'value',
        'enabled'
    ];

    protected $exportableRelationships = [
        'product_attribute'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'product_attribute_id',
        'value',
        'image',
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
        'image' => 'object',
        'enabled' => 'boolean'
    ];

    public function product_attribute(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ProductAttribute::class);
    }
}
