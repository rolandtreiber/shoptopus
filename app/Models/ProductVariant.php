<?php

namespace App\Models;

use App\Traits\HasFiles;
use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Translatable\HasTranslations;

/**
 * @method static find($variant)
 * @property string $id
 * @property string $product_id
 * @property double $price
 */
class ProductVariant extends SearchableModel implements Auditable
{
    use HasFactory;
    use HasTranslations;
    use HasFiles;
    use HasUUID;
    use \OwenIt\Auditing\Auditable;

    public $translatable = ['description'];

    protected $appends = ['final_price'];

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
        'stock'
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
     * Calculate the final price
     */
    public function getFinalPriceAttribute()
    {
        return $this->price;
    }

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

    public function updateParentStock() {
        /** @var Product $product */
        $product = $this->product;
        $variantSumStock = $product->productVariants->pluck('stock')->sum();
        $product->stock = $variantSumStock;
        $product->save();
    }

}
