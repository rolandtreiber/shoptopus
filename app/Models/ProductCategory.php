<?php

namespace App\Models;

use App\Enums\AvailabilityStatuses;
use App\Traits\HasFile;
use App\Traits\HasUUID;
use Carbon\Traits\Date;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Translatable\HasTranslations;

/**
 * @method static count()
 * @method static filtered(array $array, Request $request)
 * @method static root()
 * @property mixed|string[]|null $menu_image
 * @property mixed|string[]|null $header_image
 * @property boolean $enabled
 * @property Collection $children
 * @property Date $updated_at
 * @property mixed $id
 */
class ProductCategory extends SearchableModel implements Auditable
{
    use HasFactory, SoftDeletes, HasTranslations, HasFile;
    use HasUUID;
    use \OwenIt\Auditing\Auditable;

    public $translatable = ['name', 'description'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'enabled',
        'parent_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'parent_id' => 'string',
        'menu_image' => 'object',
        'header_image' => 'object',
        'enabled' => 'boolean'
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function children(): HasMany
    {
        $result = $this->hasMany(ProductCategory::class, 'parent_id', 'id')->whereNotNull('parent_id');
        return $result;
    }

    /**
     * @return $this
     */
    public function setChildrenIds(): ProductCategory
    {
        $this->allChildIds = [$this->id, ...$this->children()->availability('enabled')->get()->map(function(ProductCategory $category) {
            return $category->setChildrenIds()->allChildIds;
        })->toArray()];
        return $this;
    }

    /**
     * @return array
     */
    public function childrenIds(): array
    {
        $this->allChildIds = [$this->id, ...$this->children()->availability('enabled')->get()->map(function(ProductCategory $category) {
            return [$category->id, ...$category->setChildrenIds()->allChildIds];
        })->toArray()];

        return [$this->id, ...$this->children()->availability('enabled')->get()->map(function(ProductCategory $category) {
            return $category->setChildrenIds()->allChildIds;
        })];
    }

    /**
     * Recursively selects all descendants and turns them into an array.
     * The array then can be used in the frontend filtering by category anywhere down the tree.
     * @return mixed
     */
    public function tree()
    {
        $childIds = $this->childrenIds();
        array_walk_recursive($childIds, function($a) use (&$return) { $return[] = $a; });
        return $return;
    }

    /**
     * @return BelongsToMany
     */
    public function discountRules(): BelongsToMany
    {
        return $this->belongsToMany(DiscountRule::class)->valid();
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * @param bool $immediate
     * @return BelongsToMany
     */
    public function products($immediate = false)
    {
        if ($immediate) {
            return $this->belongsToMany(Product::class);
        } else {
            return Product::whereHasCategories($this->tree());
        }
    }
}
