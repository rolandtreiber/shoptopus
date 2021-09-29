<?php

namespace App\Models;

use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Spatie\Translatable\HasTranslations;

/**
 * @property mixed $unit_price
 * @property mixed $product_id
 * @property mixed $product_variant_id
 * @property mixed $amount
 * @property mixed $order
 * @property float|mixed $full_price
 * @property float|mixed $final_price
 */
class OrderProduct extends MorphPivot
{
    use HasTranslations;
    use HasUUID;

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

    /**
     * @return BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
