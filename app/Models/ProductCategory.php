<?php

namespace App\Models;

use App\Traits\HasFile;
use App\Traits\HasUUID;
use Carbon\Traits\Date;
use Illuminate\Http\Request;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Support\Collection;
use Spatie\Translatable\HasTranslations;
use OwenIt\Auditing\Contracts\Auditable;
use Shoptopus\ExcelImportExport\Exportable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Shoptopus\ExcelImportExport\traits\HasExportable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
class ProductCategory extends SearchableModel implements Auditable, Exportable
{
    use HasFactory, SoftDeletes, HasTranslations, HasFile, HasUUID, \OwenIt\Auditing\Auditable, HasSlug, HasExportable;

    protected $exportableFields = [
        'slug',
        'name',
        'description',
        'enabled'
    ];

    protected $exportableRelationships = [
        'children',
        'discount_rules',
        'parent'
    ];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(['name'])
            ->saveSlugsTo('slug');
    }

    public $translatable = ['name', 'description'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'menu_image',
        'header_image',
        'parent_id',
        'enabled'
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

//    /**
//     * Get one level deep subcategories
//     * @return \Illuminate\Database\Eloquent\Relations\HasMany
//     */
//    public function subcategories() : \Illuminate\Database\Eloquent\Relations\HasMany
//    {
//        return $this->hasMany(ProductCategory::class, 'parent_id');
//    }
//
//    /**
//     * Get all the children categories
//     * https://laraveldaily.com/eloquent-recursive-hasmany-relationship-with-unlimited-subcategories/
//     * @return \Illuminate\Database\Eloquent\Relations\HasMany
//     */
//    public function children_categories() : \Illuminate\Database\Eloquent\Relations\HasMany
//    {
//        return $this->hasMany(ProductCategory::class, 'parent_id')
//            ->with('subcategories');
//    }
//
//    /**
//     * Get a collection of recursive categories
//     * @param int|null $categoryId
//     * @return mixed
//     */
//    public static function tree($categoryId = null)
//    {
//        return $categoryId
//            ? self::where('id', $categoryId)->with('children_categories')->get()
//            : self::whereNull('parent_id')->with('children_categories')->get();
//    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id', 'id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ProductCategory::class, 'parent_id', 'id');
    }

    /**
     * Add a child category
     * @param ProductCategory $product_category
     * @return false|\Illuminate\Database\Eloquent\Model
     */
    public function addChildCategory(ProductCategory $product_category) : \Illuminate\Database\Eloquent\Model|bool
    {
        return $this->children()->save($product_category);
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
    public function discount_rules(): BelongsToMany
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
    public function products(bool $immediate = true) : BelongsToMany
    {
        if ($immediate) {
            return $this->belongsToMany(Product::class);
        } else {
            return Product::whereHasCategories($this->tree());
        }
    }
}
