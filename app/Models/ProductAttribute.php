<?php

namespace App\Models;

use App\Traits\HasUUID;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use App\Http\Requests\ListRequest;
use Spatie\Translatable\HasTranslations;
use OwenIt\Auditing\Contracts\Auditable;
use Shoptopus\ExcelImportExport\Exportable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Shoptopus\ExcelImportExport\traits\HasExportable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @method static count()
 * @method static find(mixed $attributeId)
 * @method static filtered(array $array, ListRequest $request)
 * @property string $id
 * @property mixed $options
 * @property mixed $image
 * @property mixed $type
 * @property string $product_attribute_id
 * @property double $price
 */
class ProductAttribute extends SearchableModel implements Auditable, Exportable
{
    use HasFactory, SoftDeletes, HasTranslations, HasUUID, \OwenIt\Auditing\Auditable, HasSlug, HasExportable;

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

    protected $exportableFields = [
        'slug',
        'name',
        'type',
        'enabled'
    ];

    protected $exportableRelationships = [
        'options'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type',
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

    /**
     * @return BelongsToMany
     */
    public function products() : BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('product_attribute_option_id')
            ->using(ProductProductAttribute::class);
    }

    /**
     * @return HasMany
     */
    public function options(): HasMany
    {
        return $this->hasMany(ProductAttributeOption::class);
    }
}
