<?php

namespace App\Models;

use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphPivot;

class ProductProductAttribute extends MorphPivot
{
    use HasUUID;

    protected $appends = ['option'];

    protected $fillable = [
        'product_id',
        'product_attribute_id',
        'product_attribute_option_id',
    ];

    /**
     * @return Model|HasOne|object|null
     */
    public function getOptionAttribute()
    {
        return $this->hasOne(ProductAttributeOption::class, 'id', 'product_attribute_option_id')->first();
    }
}
