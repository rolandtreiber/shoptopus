<?php

namespace App\Models;

use App\Helpers\GeneralHelper;
use App\Traits\HasFiles;
use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Translatable\HasTranslations;

/**
 * @method static count()
 * @method static find(int $productId)
 * @method static filtered(array[] $array)
 * @property mixed $name
 * @property mixed $price
 * @property mixed $id
 */
class Product extends SearchableModel implements Auditable
{
    use HasFactory;
    use HasTranslations;
    use HasFiles;
    use HasUUID;
    use \OwenIt\Auditing\Auditable;

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
        'final_price' => 'decimal:2'
    ];

    /**
     * @param $discounts
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
    public function getFinalPriceAttribute()
    {
        $discountRules = $this->discountRules->map(function($rule) {
            return [
                'id' => $rule->id,
                'amount' => $rule->amount,
                'type' => $rule->type,
            ];
        })->toArray();
        $categoriesWithDiscountRules = $this->categories()->get();
        foreach ($categoriesWithDiscountRules as $category) {
            $discountRules = array_merge($discountRules, $category->discountRules->map(function($rule) use ($discountRules) {
                return [
                    'id' => $rule->id,
                    'amount' => $rule->amount,
                    'type' => $rule->type,
                ];
            })->toArray());
        }
        $basePrice = $this->price;
        $discounts = array_map(function($rule) use ($basePrice) {
            return $basePrice - GeneralHelper::getDiscountedValue($rule['type'], $rule['amount'], $basePrice);
        }, array_unique($discountRules, SORT_REGULAR));
        dd($this->calculateDiscountAmount($discounts));
        return $this->price - $this->calculateDiscountAmount($discounts);
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
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(ProductCategory::class);
    }

    /**
     * @return BelongsToMany
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(ProductTag::class);
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
    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(ProductAttribute::class)->withPivot('product_attribute_option_id')->using(ProductProductAttribute::class);
    }

    /**
     * @return BelongsToMany
     */
    public function discountRules(): BelongsToMany
    {
        return $this->belongsToMany(DiscountRule::class);
    }
}
