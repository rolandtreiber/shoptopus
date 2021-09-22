<?php

namespace App\Models;

use App\Traits\HasFiles;
use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Translatable\HasTranslations;

/**
 * @method static find($variant)
 */
class ProductVariant extends Model implements Auditable
{
    use HasFactory;
    use HasTranslations;
    use HasFiles;
    use HasUUID;
    use \OwenIt\Auditing\Auditable;

    public $translatable = ['description'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'description',
        'data',
        'price',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'product_id' => 'string',
        'price' => 'decimal:2',
    ];

    /**
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return BelongsToMany
     */
    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(ProductAttribute::class)->withPivot('product_attribute_option_id')->using(VariantAttribute::class);
    }

}
