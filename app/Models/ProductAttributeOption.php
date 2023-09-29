<?php

namespace App\Models;

use App\Traits\HasFile;
use App\Traits\HasNote;
use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Shoptopus\ExcelImportExport\Exportable;
use Shoptopus\ExcelImportExport\Importable;
use Shoptopus\ExcelImportExport\traits\HasExportable;
use Shoptopus\ExcelImportExport\traits\HasImportable;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

/**
 * @property mixed $image
 * @property mixed $type
 * @property mixed $product_attribute_id
 * @property Collection<ProductVariant> $product_variants
 * @property Collection<Product> $products
 * @property mixed $value
 */
class ProductAttributeOption extends SearchableModel implements Auditable, Exportable, Importable
{
    use HasFactory, SoftDeletes, HasTranslations, HasFile, HasUUID, \OwenIt\Auditing\Auditable, HasSlug, HasExportable, HasImportable, HasNote;

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
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
        'enabled',
    ];

    /**
     * @var array
     */
    protected $importableFields = [
        'name',
        'value',
        'enabled' => [
            'description' => '0 = disabled, 1 = enabled',
            'validation' => 'boolean',
        ],
    ];

    protected $importableRelationships = [
        'product_attribute',
    ];

    protected $exportableRelationships = [
        'product_attribute',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'product_attribute_id',
        'value',
        'image',
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
        'image' => 'object',
        'enabled' => 'boolean',
    ];

    public function product_attribute(): BelongsTo
    {
        return $this->belongsTo(ProductAttribute::class);
    }

    /**
     * @return BelongsToMany
     */
    public function product_variants(): BelongsToMany
    {
        return $this->belongsToMany(ProductVariant::class, 'product_attribute_product_variant', 'product_attribute_option_id', 'product_variant_id');
    }

    /**
     * @return BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_product_attribute', 'product_attribute_option_id', 'product_id');
    }

}
