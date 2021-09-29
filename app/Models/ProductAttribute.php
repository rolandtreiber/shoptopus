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
class ProductAttribute extends SearchableModel implements Auditable
{
    use HasFactory, SoftDeletes, HasTranslations, HasFile;
    use HasUUID;
    use \OwenIt\Auditing\Auditable;

    public $translatable = ['name'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'image',
        'type'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'image' => 'object',
    ];

    /**
     * @return HasMany
     */
    public function options(): HasMany
    {
        return $this->HasMany(ProductAttributeOption::class);
    }
}
