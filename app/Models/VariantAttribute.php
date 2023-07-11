<?php

namespace App\Models;

use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Relations\MorphPivot;

/**
 * @property string $product_attribute_id
 * @property string $product_variant_id
 * @property string $product_attribute_option_id
 */
class VariantAttribute extends MorphPivot
{
    use HasUUID;

    protected $appends = ['option'];

    protected $fillable = [
        'product_attribute_id',
        'product_variant_id',
        'product_attribute_option_id',
    ];

    public function getOptionAttribute()
    {
        return $this->hasOne(ProductAttributeOption::class, 'id', 'product_attribute_option_id')
            ->first();
    }
}
