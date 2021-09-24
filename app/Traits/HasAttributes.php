<?php

namespace App\Traits;

use App\Models\ProductAttribute;

trait HasAttributes {

    /**
     * @param $model
     * @param $request
     */
    public function handleAttributes($model, $request)
    {
        $model->attributes()->detach();
        if ($request->product_attributes) {
            foreach ($request->product_attributes as $attributeId => $attributeOptionId) {
                $attribute = ProductAttribute::find($attributeId);
                if ($attribute) {
                    $model->attributes()->attach($attribute, ['product_attribute_option_id' => $attributeOptionId]);
                }
            }
        }
    }

}
