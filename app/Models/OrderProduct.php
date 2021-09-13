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

    public $incrementing = true;
}
