<?php

namespace App\Models;

use App\Enums\ProductStatuses;
use App\Helpers\GeneralHelper;
use App\Traits\HasFiles;
use App\Traits\HasEventLogs;
use App\Traits\HasRatings;
use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable;
use Shoptopus\ExcelImportExport\Exportable;
use Shoptopus\ExcelImportExport\traits\HasExportable;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

/**
 * @method static count()
 * @method static find(int $productId)
 * @method static filtered(array[] $array)
 * @property mixed $name
 * @property mixed $price
 * @property mixed $id
 * @property float $final_price
 * @property int $status
 * @property int $stock
 * @property int $purchase_count
 * @property int $backup_stock
 * @property \Illuminate\Database\Eloquent\Collection $tags
 * @property Collection $categories
 * @property \Illuminate\Database\Eloquent\Collection $fileContents
 * @property FileContent $coverPhoto
 * @property string $updated_at
 * @property mixed $sku
 * @property mixed $cover_photo
 */
class Product extends SearchableModel implements Auditable, Exportable
{
    use HasFactory;
    use HasTranslations;
    use HasFiles;
    use HasRatings;
    use HasEventLogs;
    use HasUUID;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;
    use HasSlug;
    use HasExportable;

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    protected $exportableFields = [
        'name',
        'slug',
        'short_description',
        'description',
        'price',
        'status',
        'purchase_count',
        'stock',
        'backup_stock',
        'rating',
        'final_price',
        'sku'
    ];

    protected $exportableRelationships = [
        'productCategories',
        'productAttributes',
        'productTags',
        'productVariants',
        'discountRules'
    ];

    public $translatable = ['name', 'short_description', 'description'];

    protected $appends = ['final_price'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'short_description',
        'description',
        'price',
        'status',
        'purchase_count',
        'stock',
        'backup_stock',
        'rating',
        'sku'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'name' => 'object',
        'status' => 'integer',
        'purchase_count' => 'integer',
        'stock' => 'integer',
        'backup_stock' => 'integer',
        'price' => 'decimal:2',
        'final_price' => 'decimal:2',
        'rating' => 'decimal:2',
        'cover_photo' => 'object'
    ];

    /**
     * Updated at accessor
     *
     * @param $date
     * @return string
     */
    public function getUpdatedAtAttribute($date): string
    {
        return Carbon::parse($date)->format('Y-m-d H:i:s');
    }

    public function scopeView($query, $view)
    {
        switch ($view) {
            case 'active':
                $query->where('status', ProductStatuses::Active);
                break;
            case 'discontinued':
                $query->where('status', ProductStatuses::Discontinued);
                break;
            case 'provisional':
                $query->where('status', ProductStatuses::Provisional);
                break;
        }
    }

    /**
     * @param $discounts
     * @return float|int|mixed
     */
    private function calculateDiscountAmount($discounts)
    {
        if (config('shoptopus.discount_rules.allow_discount_stacking') === true) {
            // Discounts stacked and all applied
            return array_sum($discounts);
        } else {
            // Only one discount applied. It is either the lowest or the highest
            switch (config('shoptopus.discount_rules.applied_discount')) {
                case 'highest':
                    return max($discounts);
                default:
                    return min($discounts);
            }
        }
    }

    /**
     * Calculate the final price
     */
    public function getFinalPriceAttribute($price = null)
    {
        if (!$price) {
            $price = $this->price;
        }
        $discountRules = $this->discountRules->map(function($rule) {
            return [
                'id' => $rule->id,
                'amount' => $rule->amount,
                'type' => $rule->type,
            ];
        })->toArray();
        $categoriesWithDiscountRules = $this->productCategories()->get();
        foreach ($categoriesWithDiscountRules as $category) {
            $discountRules = array_merge($discountRules, $category->discountRules->map(function($rule) use ($discountRules) {
                return [
                    'id' => $rule->id,
                    'amount' => $rule->amount,
                    'type' => $rule->type,
                ];
            })->toArray());
        }
        $basePrice = $price;
        if (sizeof($discountRules) > 0) {
            $discounts = array_map(function($rule) use ($basePrice) {
                return $basePrice - GeneralHelper::getDiscountedValue($rule['type'], $rule['amount'], $basePrice);
            }, array_unique($discountRules, SORT_REGULAR));
            return $price - $this->calculateDiscountAmount($discounts);
        } else {
            return $price;
        }
    }

    /**
     * @param $query
     * @param array|null $tags
     */
    public function scopeWhereHasTags($query, ?array $tags)
    {
        if ($tags && count($tags) > 0) {
            $productIdsWithTags = DB::table('product_product_tag')->whereIn('product_tag_id', $tags)->pluck('product_id');
            $query->whereIn('id', $productIdsWithTags);
        }
    }

    /**
     * @param $query
     * @param array|null $categories
     */
    public function scopeWhereHasCategories($query, ?array $categories)
    {
        if ($categories && count($categories) > 0) {
            $productIdsWithTags = DB::table('product_product_category')->whereIn('product_category_id', $categories)->pluck('product_id');
            $query->whereIn('id', $productIdsWithTags);
        }
    }

    /**
     * @param $query
     * @param array|null $attributeOptions
     */
    public function scopeWhereHasAttributeOptions($query, ?array $attributeOptions)
    {
        if ($attributeOptions && count($attributeOptions) > 0) {
            $productIdsWithAttributeOptions = DB::table('product_product_attribute')->whereIn('product_attribute_option_id', $attributeOptions)->pluck('product_id');
            $productIdsWithVariantWithAttributeOptions = DB::table('product_attribute_product_variant')
                ->whereIn('product_attribute_option_id', $attributeOptions)
                ->join('product_variants', 'product_attribute_product_variant.product_variant_id', '=', 'product_variants.id')
                ->pluck('product_variants.product_id');
            $query->whereIn('id', $productIdsWithAttributeOptions->merge($productIdsWithVariantWithAttributeOptions)->unique());
        }
    }

    /**
     * @return BelongsToMany
     */
    public function productTags(): BelongsToMany
    {
        return $this->belongsToMany(ProductTag::class);
    }

    /**
     * @return BelongsToMany
     */
    public function productCategories(): BelongsToMany
    {
        return $this->belongsToMany(ProductCategory::class);
    }

    /**
     * @return HasMany
     */
    public function productVariants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * @return BelongsToMany
     */
    public function productAttributes(): BelongsToMany
    {
        return $this->belongsToMany(ProductAttribute::class)->withPivot('product_attribute_option_id')->using(ProductProductAttribute::class);
    }

    /**
     * @return BelongsToMany
     */
    public function discountRules(): BelongsToMany
    {
        return $this->belongsToMany(DiscountRule::class)->valid();
    }

    /**
     * @return array
     */
    public function getAttributedTranslatedNameAttribute(): array
    {
        $attributes = $this->productAttributes;
        if (!$attributes || count($attributes) === 0) {
            return $this->getTranslations('name');
        }
        $languages = config('app.locales_supported');
        $elements = [];
        foreach ($languages as $languageKey => $language) {
            $text = $this->setLocale($languageKey)->name . ' - ';
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
     * @param array|null $categoryIds
     */
    public function handleCategories(?array $categoryIds = [])
    {
        $this->productCategories()->detach();
        $this->productCategories()->sync($categoryIds);
    }

    /**
     * @param array|null $tagIds
     */
    public function handleTags(?array $tagIds = [])
    {
        $this->tags()->detach();
        $this->tags()->sync($tagIds);
    }

}
