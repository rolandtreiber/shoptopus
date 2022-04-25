<?php

namespace App\Models;

use App\Traits\HasUUID;
use App\Traits\HasFiles;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Translatable\HasTranslations;
use Shoptopus\ExcelImportExport\Exportable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Shoptopus\ExcelImportExport\traits\HasExportable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @method static find($variant)
 * @property string $id
 * @property string $product_id
 * @property double $price
 * @property mixed $sku
 * @property boolean $enabled
 */
class ProductVariant extends SearchableModel implements Auditable, Exportable
{
    use HasFactory, HasTranslations, HasFiles, HasUUID, \OwenIt\Auditing\Auditable, SoftDeletes, HasSlug, HasExportable;

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(['product.name'])
            ->saveSlugsTo('slug');
    }

    public $translatable = ['description'];

    protected $appends = ['final_price', 'name'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'description',
        'data',
        'price',
        'stock',
        'sku',
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
        'product_id' => 'string',
        'price' => 'decimal:2',
        'enabled' => 'boolean'
    ];

    /**
     * @var string[]
     */
    protected $exportableFields = [
        'slug',
        'name',
        'sku',
        'final_price'
    ];

    /**
     * @var string[]
     */
    protected $exportableRelationships = [
        'product'
    ];

    /**
     * Calculate the final price
     */
    public function getFinalPriceAttribute()
    {
        return $this->product->getFinalPriceAttribute($this->price);
    }

    /**
     * Get the product for the product variant
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the cover image for the product variant
     * @return null|FileContent
     */
    public function cover_image() : ?FileContent
    {
        return optional($this->images())->first();
    }

    /**
     * @return BelongsToMany
     */
    public function product_variant_attributes(): BelongsToMany
    {
        return $this->belongsToMany(ProductAttribute::class)
            ->withPivot('product_attribute_option_id')
            ->using(VariantAttribute::class);
    }

    public function updateParentStock()
    {
        /** @var Product $product */
        $product = $this->product;
        $variantSumStock = $product->product_variants->pluck('stock')->sum();
        $product->stock = $variantSumStock;
        $product->save();
    }

    /**
     * @return array
     */
    public function getNameAttribute(): array
    {
        $attributes = $this->product_variant_attributes;
        $languages = config('app.locales_supported');
        $elements = [];

        foreach ($languages as $languageKey => $language) {
            $text = $this->product->setLocale($languageKey)->name . ' - ';
            $attributeTexts = [];
            foreach ($attributes as $attribute) {
                $option = $attribute->pivot->option;
                $attributeTexts[] =  '(' . $attribute->setLocale($languageKey)->name . ') ' . $option->setLocale($languageKey)->name;
            }
            $elements[$languageKey] = $text . implode(', ', $attributeTexts);
        }
        return $elements;
    }
}
