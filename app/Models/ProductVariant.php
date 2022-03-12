<?php

namespace App\Models;

use App\Traits\HasFiles;
use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable;
use Shoptopus\ExcelImportExport\Exportable;
use Shoptopus\ExcelImportExport\traits\HasExportable;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

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

    protected $appends = ['final_price'];

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
        'sku'
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
     * Calculate the final price
     */
    public function getFinalPriceAttribute()
    {
        return $this->product->getFinalPriceAttribute($this->price);
    }

    /**
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return BelongsToMany
     */
    public function productVariantAttributes(): BelongsToMany
    {
        return $this->belongsToMany(ProductAttribute::class)->withPivot('product_attribute_option_id')->using(VariantAttribute::class);
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
        $attributes = $this->productVariantAttributes;
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

    /**
     * @return string|null
     */
    public function coverImage(): ?string
    {
        $imagesCount = DB::table('file_contents')->where('fileable_type', get_class($this))->where('fileable_id', $this->id)->count();
        if ($imagesCount) {
            $img = FileContent::where('fileable_type', ProductVariant::class)->where('fileable_id', $this->id)->first();
            return $img->url;
        } else {
            return null;
        }
    }

}
