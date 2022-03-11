<?php

namespace App\Models;

use App\Http\Requests\ListRequest;
use App\Traits\HasFile;
use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Shoptopus\ExcelImportExport\Exportable;
use Shoptopus\ExcelImportExport\traits\HasExportable;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

/**
 * @method static count()
 * @method static find(mixed $attributeId)
 * @method static filtered(array $array, ListRequest $request)
 * @property string $id
 * @property mixed $options
 * @property mixed $image
 * @property mixed $type
 * @property string $product_attribute_id
 * @property double $price
 */
class ProductAttribute extends SearchableModel implements Auditable, Exportable
{
    use HasFactory, SoftDeletes, HasTranslations;
    use HasUUID;
    use \OwenIt\Auditing\Auditable;
    use HasSlug;
    use HasExportable;

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(['name'])
            ->saveSlugsTo('slug');
    }

    public $translatable = ['name'];

    protected $exportableFields = [
        'slug',
        'type',
        'enabled'
    ];

    protected $exportableRelationships = [
        'options'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type',
        'enabled'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'image' => 'object',
        'enabled' => 'boolean'
    ];

    /**
     * @return HasMany
     */
    public function options(): HasMany
    {
        return $this->HasMany(ProductAttributeOption::class);
    }
}
