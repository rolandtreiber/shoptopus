<?php

namespace App\Models;

use App\Http\Requests\ListRequest;
use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
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
 * @method static find(mixed $attributeId)
 * @method static filtered(array $array, ListRequest $request)
 *
 * @property string $id
 * @property mixed $options
 * @property mixed $image
 * @property mixed $type
 * @property string $product_attribute_id
 * @property float $price
 */
class ProductAttribute extends SearchableModel implements Auditable, Exportable, Importable
{
    use HasFactory, SoftDeletes, HasTranslations, HasUUID, \OwenIt\Auditing\Auditable, HasSlug, HasExportable, HasImportable;

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(['name'])
            ->saveSlugsTo('slug');
    }

    public $translatable = ['name'];

    protected $exportableFields = [
        'slug',
        'name',
        'type',
        'enabled',
    ];

    protected $importableFields = [
        'name' => [
            'validation' => ['unique:product_attributes,name'],
        ],
        'type' => [
            'description' => '1 = Text, 2 = Image, 3 = Color',
            'validation' => ['numeric', 'min:1', 'max:3'],
        ],
        'enabled' => [
            'description' => '0 = disabled, 1 = enabled',
            'validation' => 'boolean',
        ],
    ];

    protected $exportableRelationships = [
        'options',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type',
        'image',
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
        'image' => 'object',
        'enabled' => 'boolean',
    ];

    /**
     * @return BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('product_attribute_option_id')
            ->using(ProductProductAttribute::class);
    }

    /**
     * @return HasMany
     */
    public function options(): HasMany
    {
        return $this->hasMany(ProductAttributeOption::class);
    }
}
