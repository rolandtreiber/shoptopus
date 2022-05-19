<?php

namespace App\Models;

use App\Traits\HasUUID;
use App\Traits\HasFiles;
use App\Traits\HasRatings;
use App\Enums\ProductStatus;
use App\Traits\HasEventLogs;
use Spatie\Sluggable\HasSlug;
use Illuminate\Support\Carbon;
use App\Helpers\GeneralHelper;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Support\Facades\DB;
use Spatie\Translatable\HasTranslations;
use OwenIt\Auditing\Contracts\Auditable;
use Shoptopus\ExcelImportExport\Exportable;
use Shoptopus\ExcelImportExport\Importable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Shoptopus\ExcelImportExport\traits\HasExportable;
use Shoptopus\ExcelImportExport\traits\HasImportable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
 * @property \Illuminate\Database\Eloquent\Collection $fileContents
 * @property FileContent $coverPhoto
 * @property string $updated_at
 * @property mixed $sku
 * @property mixed $cover_photo
 */
class Product extends SearchableModel implements Auditable, Exportable, Importable
{
    use HasFactory,
        HasTranslations,
        HasFiles,
        HasRatings,
        HasEventLogs,
        HasUUID,
        \OwenIt\Auditing\Auditable,
        SoftDeletes,
        HasSlug,
        HasExportable,
        HasImportable;

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

    protected $importableFields = [
        'name',
        'short_description',
        'description',
        'price' => [
            'validation' => ['numeric']
        ],
        'status' => [
            'description' => '1 = Provisional, 2 = Active, 3 = Discontinued',
            'validation' => ['integer', 'min:1', 'max:3']
        ],
        'stock' => [
            'validation' => ['integer', 'min:0']
        ],
        'backup_stock' => [
            'validation' => ['integer', 'min:0']
        ],
        'sku' => [
            'validation' => ['max:20', 'unique:products,sku']
        ]
    ];

    protected $exportableRelationships = [
        'product_categories',
        'product_attributes',
        'product_tags',
        'product_variants',
        'discount_rules'
    ];

    protected $importableRelationships = [
        'product_categories',
        'product_attributes',
        'product_tags',
        'discount_rules'
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
        'headline',
        'subtitle',
        'status',
        'purchase_count',
        'stock',
        'backup_stock',
        'rating',
        'sku',
        'deleted_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
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
     * @return BelongsToMany
     */
    public function discount_rules(): BelongsToMany
    {
        return $this->belongsToMany(DiscountRule::class)->valid();
    }

    /**
     * @return BelongsToMany
     */
    public function product_categories(): BelongsToMany
    {
        return $this->belongsToMany(ProductCategory::class);
    }

    /**
     * @return BelongsToMany
     */
    public function product_tags(): BelongsToMany
    {
        return $this->belongsToMany(ProductTag::class);
    }

    /**
     * @return BelongsToMany
     */
    public function product_attributes(): BelongsToMany
    {
        return $this->belongsToMany(ProductAttribute::class)
            ->withPivot('product_attribute_option_id')
            ->using(ProductProductAttribute::class);
    }

    /**
     * @return HasMany
     */
    public function product_variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }


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
                $query->where('status', ProductStatus::Active);
                break;
            case 'discontinued':
                $query->where('status', ProductStatus::Discontinued);
                break;
            case 'provisional':
                $query->where('status', ProductStatus::Provisional);
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
        $discount_rules = $this->discount_rules->map(function($rule) {
            return [
                'id' => $rule->id,
                'amount' => $rule->amount,
                'type' => $rule->type,
            ];
        })->toArray();
        $categoriesWithDiscountRules = $this->product_categories()->get();
        foreach ($categoriesWithDiscountRules as $category) {
            $discount_rules = array_merge($discount_rules, $category->discount_rules->map(function($rule) use ($discount_rules) {
                return [
                    'id' => $rule->id,
                    'amount' => $rule->amount,
                    'type' => $rule->type,
                ];
            })->toArray());
        }
        $basePrice = $price;
        if (sizeof($discount_rules) > 0) {
            $discounts = array_map(function($rule) use ($basePrice) {
                return $basePrice - GeneralHelper::getDiscountedValue($rule['type'], $rule['amount'], $basePrice);
            }, array_unique($discount_rules, SORT_REGULAR));
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
            $productIdsWithAttributeOptions = DB::table('product_product_attribute')
                ->whereIn('product_attribute_option_id', $attributeOptions)
                ->pluck('product_id');

            $productIdsWithVariantWithAttributeOptions = DB::table('product_attribute_product_variant')
                ->whereIn('product_attribute_option_id', $attributeOptions)
                ->join('product_variants', 'product_attribute_product_variant.product_variant_id', '=', 'product_variants.id')
                ->pluck('product_variants.product_id');

            $query->whereIn('id', $productIdsWithAttributeOptions->merge($productIdsWithVariantWithAttributeOptions)->unique());
        }
    }

    /**
     * @return array
     */
    public function getAttributedTranslatedNameAttribute(): array
    {
        $attributes = $this->product_attributes;
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
        $this->product_categories()->detach();
        $this->product_categories()->sync($categoryIds);
    }

    /**
     * @param array|null $tagIds
     */
    public function handleTags(?array $tagIds = [])
    {
        $this->product_tags()->detach();
        $this->product_tags()->sync($tagIds);
    }

}
