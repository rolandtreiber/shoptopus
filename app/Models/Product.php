<?php

namespace App\Models;

use App\Enums\ProductStatus;
use App\Helpers\GeneralHelper;
use App\Traits\HasEventLogs;
use App\Traits\HasFiles;
use App\Traits\HasNote;
use App\Traits\HasRatings;
use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use JeroenG\Explorer\Application\Explored;
use Laravel\Scout\Searchable;
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
 * @method static find(int $productId)
 * @method static filtered(array[] $array)
 *
 * @property mixed $name
 * @property mixed $price
 * @property mixed $id
 * @property float $final_price
 * @property int $status
 * @property Collection<Order> $orders
 * @property OrderProduct $pivot
 * @property string $sku
 * @property string $slug
 * @property int $stock
 * @property int $purchase_count
 * @property int $backup_stock
 * @property Collection $tags
 * @property Collection $fileContents
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property FileContent|array<string, string>|null $cover_photo
 * @property FileContent|null $coverPhoto
 * @property string $parent_id
 * @property float $rating
 * @property boolean $virtual
 * @property integer $weight
 * @property Collection<ProductAttribute>|null $product_attributes
 * @property Collection<ProductVariant>|null $product_variants
 */
class Product extends SearchableModel implements Auditable, Exportable, Importable, Explored
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
        HasImportable,
        Searchable,
        HasNote;

    public function mappableAs(): array
    {
        return [
            'id' => 'keyword',
            'price' => 'float',
            'name' => 'nested',
            'headline' => 'nested',
            'description' => 'nested',
            'purchase_count' => 'long',
            'rating' => 'float',
            'created_at' => 'date',
            'updated_at' => 'date',
        ];
    }

    public function toSearchableArray(): array
    {
        $result = [
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'name' => $this->getTranslations('name'),
            'headline' => $this->getTranslations('headline'),
            'description' => $this->getTranslations('description'),
            'slug' => $this->slug,
            'status' => $this->status,
            'purchase_count' => $this->purchase_count,
            'stock' => $this->stock,
            'price' => $this->price,
            'backup_stock' => $this->backup_stock,
            'rating' => $this->rating,
            'created_at' => $this->created_at,
            'categories' => str_replace('-', '', implode(' ', $this->product_categories->pluck('id')->toArray())),
            'tags' => str_replace('-', '', implode(' ', $this->product_tags->pluck('id')->each(function ($item) {
            return str_replace('-', '', $item);
            })->toArray())),
        ];

        return $result;
    }

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
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
        'weight',
        'virtual',
        'sku',
    ];

    protected $importableFields = [
        'name',
        'short_description',
        'description',
        'price' => [
            'validation' => ['numeric'],
        ],
        'status' => [
            'description' => '1 = Provisional, 2 = Active, 3 = Discontinued',
            'validation' => ['integer', 'min:1', 'max:3'],
        ],
        'stock' => [
            'validation' => ['integer', 'min:0'],
        ],
        'backup_stock' => [
            'validation' => ['integer', 'min:0'],
        ],
        'weight' => [
            'validation' => ['integer', 'min:0'],
        ],
        'virtual' => [
            'validation' => ['boolean'],
        ],
        'sku' => [
            'validation' => ['max:20', 'unique:products,sku'],
        ],
    ];

    protected $exportableRelationships = [
        'product_categories',
        'product_attributes',
        'product_tags',
        'product_variants',
        'discount_rules',
    ];

    protected $importableRelationships = [
        'product_categories',
        'product_attributes',
        'product_tags',
        'discount_rules',
    ];

    public $translatable = ['name', 'short_description', 'description', 'headline', 'subtitle'];

    protected $appends = ['final_price'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
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
        'deleted_at',
        'cover_photo',
        'weight',
        'virtual'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
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
        'cover_photo' => 'object',
        'virtual' => 'boolean'
    ];

    public function discount_rules(): BelongsToMany
    {
        return $this->belongsToMany(DiscountRule::class)->valid();
    }

    public function product_categories(): BelongsToMany
    {
        return $this->belongsToMany(ProductCategory::class);
    }

    public function product_tags(): BelongsToMany
    {
        return $this->belongsToMany(ProductTag::class);
    }

    public function product_attributes(): BelongsToMany
    {
        return $this->belongsToMany(ProductAttribute::class)
            ->withPivot('product_attribute_option_id')
            ->using(ProductProductAttribute::class);
    }

    public function product_variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function recalculateStock()
    {
        $this->refresh();

        $this->update(['stock' => $this->product_variants->pluck('stock')->sum()]);
    }

    /**
     * Calculate the total discounts
     */
    private function calculateDiscountAmount($discounts): mixed
    {
        if (config('shoptopus.discount_rules.allow_discount_stacking') === true) {
            // Discounts stacked and all applied
            return array_sum($discounts);
        } else {
            // Only one discount applied. It is either the lowest or the highest
            return match (config('shoptopus.discount_rules.applied_discount')) {
                'highest' => max($discounts),
                default => min($discounts),
            };
        }
    }

    /**
     * Calculate the final price with discounts
     *
     * @param float|null $price
     */
    public function getFinalPriceAttribute(float $price = null): mixed
    {
        if (! $price) {
            $price = $this->price;
        }

        $discount_rules = $this->discount_rules->map(fn ($rule) => [
            'id' => $rule->id,
            'amount' => $rule->amount,
            'type' => $rule->type,
        ])->toArray();

        foreach ($this->product_categories()->get() as $category) {
            $discount_rules = array_merge($discount_rules, $category->discount_rules->map(fn ($rule) => [
                'id' => $rule->id,
                'amount' => $rule->amount,
                'type' => $rule->type,
            ])->toArray());
        }

        $basePrice = $price;

        if (count($discount_rules) > 0) {
            $discounts = array_map(function ($rule) use ($basePrice) {
                return $basePrice - GeneralHelper::getDiscountedValue($rule['type'], $rule['amount'], $basePrice);
            }, array_unique($discount_rules, SORT_REGULAR));

            return $price - $this->calculateDiscountAmount($discounts);
        } else {
            return $price;
        }
    }

    /**
     * Updated at accessor
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
     * @param $query
     * @param array|null $tags
     * @return void
     */
    public function scopeWhereHasTags($query, ?array $tags): void
    {
        if (($tags !== null) && count($tags) > 0) {
            $productIdsWithTags = DB::table('product_product_tag')->whereIn('product_tag_id', $tags)->pluck('product_id');
            $query->whereIn('id', $productIdsWithTags);
        }
    }

    /**
     * @param $query
     * @param array|null $categories
     * @return void
     */
    public function scopeWhereHasCategories($query, ?array $categories)
    {
        if (($categories !== null) && count($categories) > 0) {
            $productIdsWithTags = DB::table('product_product_category')->whereIn('product_category_id', $categories)->pluck('product_id');
            $query->whereIn('id', $productIdsWithTags);
        }
    }

    /**
     * @param $query
     * @param array|null $attributeOptions
     * @return void
     */
    public function scopeWhereHasAttributeOptions($query, ?array $attributeOptions)
    {
        if (($attributeOptions !== null) && count($attributeOptions) > 0) {
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

    public function getAttributedTranslatedNameAttribute(): array
    {
        $attributes = $this->product_attributes;
        if (($attributes === null) || count($attributes) === 0) {
            return $this->getTranslations('name');
        }
        $languages = config('app.locales_supported');
        $elements = [];
        foreach ($languages as $languageKey => $language) {
            $text = $this->setLocale($languageKey)->name.' - ';
            $attributeTexts = [];
            foreach ($attributes as $attribute) {
                $option = $attribute->pivot->option;
                // @phpstan-ignore-next-line
                $attributeTexts[] = '('.$attribute->setLocale($languageKey)->name.') '.$option->setLocale($languageKey)->name;
            }
            $elements[$languageKey] = $text.implode(', ', $attributeTexts);
        }

        return $elements;
    }

    public function handleCategories(?array $categoryIds = [])
    {
        $this->product_categories()->detach();
        $this->product_categories()->sync($categoryIds);
    }

    public function handleTags(?array $tagIds = [])
    {
        $this->product_tags()->detach();
        $this->product_tags()->sync($tagIds);
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class)->withPivot(['id', 'name', 'amount', 'full_price', 'original_unit_price', 'unit_price', 'final_price', 'unit_discount', 'total_discount', 'product_variant_id'])->using(OrderProduct::class);
    }}
