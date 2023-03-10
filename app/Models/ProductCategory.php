<?php

namespace App\Models;

use App\Traits\HasFile;
use App\Traits\HasUUID;
use Carbon\Traits\Date;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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
 * @method static filtered(array $array, Request $request)
 * @method static root()
 *
 * @property mixed|string[]|null $menu_image
 * @property mixed|string[]|null $header_image
 * @property bool $enabled
 * @property Collection $children
 * @property Date $updated_at
 * @property mixed $id
 */
class ProductCategory extends SearchableModel implements Auditable, Exportable, Importable
{
    use HasFactory, SoftDeletes, HasTranslations, HasFile, HasUUID, \OwenIt\Auditing\Auditable, HasSlug, HasExportable, HasImportable;

    protected $exportableFields = [
        'slug',
        'name',
        'description',
        'enabled',
    ];

    protected $exportableRelationships = [
        'children',
        'discount_rules',
        'parent',
        'associated_products',
    ];

    protected $importableFields = [
        'name' => [
            'validation' => ['unique:product_categories,name'],
        ],
        'description',
        'enabled' => [
            'description' => '0 = disabled, 1 = enabled',
            'validation' => 'boolean',
        ],
    ];

    protected $importableRelationships = [
        'parent',
        'discount_rules',
        'associated_products',
    ];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
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
        'enabled',
        'deleted_at',
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
        'enabled' => 'boolean',
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
     *
     * @param  ProductCategory  $product_category
     * @return false|\Illuminate\Database\Eloquent\Model
     */
    public function addChildCategory(ProductCategory $product_category): \Illuminate\Database\Eloquent\Model|bool
    {
        return $this->children()->save($product_category);
    }

    /**
     * @return $this
     */
    public function setChildrenIds(): ProductCategory
    {
        $this->allChildIds = [$this->id, ...$this->children()->availability('enabled')->get()->map(function (ProductCategory $category) {
            return $category->setChildrenIds()->allChildIds;
        })->toArray()];

        return $this;
    }

    /**
     * @return array
     */
    public function childrenIds(): array
    {
        $this->allChildIds = [$this->id, ...$this->children()->availability('enabled')->get()->map(function (ProductCategory $category) {
            return [$category->id, ...$category->setChildrenIds()->allChildIds];
        })->toArray()];

        return [$this->id, ...$this->children()->availability('enabled')->get()->map(function (ProductCategory $category) {
            return $category->setChildrenIds()->allChildIds;
        })];
    }

    /**
     * Recursively selects all descendants and turns them into an array.
     * The array then can be used in the frontend filtering by category anywhere down the tree.
     *
     * @return mixed
     */
    public function tree()
    {
        $childIds = $this->childrenIds();
        array_walk_recursive($childIds, function ($a) use (&$return) {
        $return[] = $a;
        });

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
     * @return BelongsToMany
     * It turns out that the import and export can only find relationships when the function does not have any argument.
     */
    public function associated_products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    /**
     * @param  bool  $immediate
     * @return BelongsToMany
     */
    public function products(bool $immediate = true): BelongsToMany
    {
        if ($immediate) {
            return $this->belongsToMany(Product::class);
        } else {
            return Product::whereHasCategories($this->tree());
        }
    }
}
