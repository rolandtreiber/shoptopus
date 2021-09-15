<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Spatie\Translatable\HasTranslations;

/**
 * @property mixed $unit_price
 * @property float|mixed $price
 * @property mixed $product_id
 * @property mixed $product_variant_id
 * @property mixed $amount
 */
class OrderProduct extends MorphPivot
{
    use HasTranslations;

    protected $table = 'order_products';

    public $translatable = ['name'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'product_id' => 'string'
    ];
}
