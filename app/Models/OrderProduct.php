<?php

namespace App\Models;

use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Shoptopus\ExcelImportExport\Exportable;
use Shoptopus\ExcelImportExport\traits\HasExportable;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

/**
 * @property float $unit_price
 * @property string $product_id
 * @property string $product_variant_id
 * @property integer $amount
 * @property integer $quantity
 * @property Order $order
 * @property string $sku
 * @property float|mixed $full_price
 * @property float|mixed $final_price
 * @property float|mixed $original_unit_price
 * @property float|mixed $unit_discount
 * @property float|mixed $total_discount
 */
class OrderProduct extends MorphPivot implements Exportable
{
    use HasTranslations;
    use HasUUID;
    use HasSlug, \OwenIt\Auditing\Auditable, HasExportable;

    protected $table = 'order_product';

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(['order.slug', 'product.slug'])
            ->saveSlugsTo('slug');
    }

    public $translatable = ['name'];

    /**
     * @var string[]
     */
    protected $exportableFields = [
        'slug',
        'amount',
        'name',
    ];

    /**
     * @var string[]
     */
    protected $exportableRelationships = [
        'order',
        'product',
        'productVariant',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'product_id' => 'string',
        'product_variant_id' => 'string',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
