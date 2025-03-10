<?php

namespace App\Models;

use App\Traits\HasFiles;
use App\Traits\HasNote;
use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Contracts\Auditable;
use Shoptopus\ExcelImportExport\Exportable;
use Shoptopus\ExcelImportExport\traits\HasExportable;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

/**
 * @method static find($variant)
 *
 * @property string $id
 * @property string $product_id
 * @property float $price
 * @property mixed $sku
 * @property bool $enabled
 * @property array $attribute_options
 * @property string $name
 * @property int $stock
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Collection<FileContent> $images
 * @method images()
 */
class ProductVariant extends SearchableModel implements Auditable, Exportable
{
    use HasFactory, HasTranslations, HasFiles, HasUUID, \OwenIt\Auditing\Auditable, SoftDeletes, HasSlug, HasExportable, HasNote;

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
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
     * @var array<string>
     */
    protected $fillable = [
        'product_id',
        'description',
        'data',
        'price',
        'stock',
        'sku',
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
        'product_id' => 'string',
        'price' => 'decimal:2',
        'enabled' => 'boolean',
        'attribute_options' => 'array',
    ];

    /**
     * @var string[]
     */
    protected $exportableFields = [
        'slug',
        'name',
        'sku',
        'final_price',
    ];

    /**
     * @var string[]
     */
    protected $exportableRelationships = [
        'product',
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
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the cover image for the product variant
     */
    public function cover_image(): ?FileContent
    {
        return $this->images()?->first();
    }

    public function product_variant_attributes(): BelongsToMany
    {
        return $this->belongsToMany(ProductAttribute::class)
            ->withPivot('product_attribute_option_id')
            ->using(VariantAttribute::class);
    }

    public function getNameAttribute(): array
    {
        $attributes = $this->product_variant_attributes;
        $languages = config('app.locales_supported');
        $elements = [];

        foreach ($languages as $languageKey => $language) {
            $text = $this->product->setLocale($languageKey)->name.' - ';
            $attributeTexts = [];
            foreach ($attributes as $attribute) {
                /** @var ProductAttribute $attribute */
                $option = $attribute->pivot->option;

                // @phpstan-ignore-next-line - phpstan doesn't seem to understand that translatable fields return string after setLocale()
                $attributeTexts[] = '('.$attribute->setLocale($languageKey)->name.') '.$option->setLocale($languageKey)->name;
            }
            $elements[$languageKey] = $text.implode(', ', $attributeTexts);
        }

        return $elements;
    }
}
