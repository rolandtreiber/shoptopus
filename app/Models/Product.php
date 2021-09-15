<?php

namespace App\Models;

use App\Traits\HasFiles;
use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Translatable\HasTranslations;

/**
 * @method static count()
 * @method static find(int $productId)
 * @method static filtered(array[] $array)
 * @property mixed $name
 * @property mixed $price
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
     *
     */
    public function getFinalPriceAttribute()
    {
        return $this->price;
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
     * @return MorphMany
     */
    public function filecontents()
    {
        return $this->morphMany('App\Models\FileContent', 'fileable');
    }

    /**
     * @return BelongsToMany
     */
    public function discountRules(): BelongsToMany
    {
        return $this->belongsToMany(DiscountRule::class);
    }
}
