<?php

namespace App\Traits;

use App\Models\Product;
use App\Models\ProductAttribute;

trait HasAttributes {

    /**
     * @param $model
     * @param $request
     */
    public function handleAttributes($model, $request)
    {
        $modelClass = get_class($model);
        if ($modelClass === Product::class) {
            $model->product_attributes()->detach();
            if ($request->product_attributes) {
                foreach ($request->product_attributes as $attributeId => $attributeOptionId) {
                    $attribute = ProductAttribute::find($attributeId);
                    if ($attribute) {
                        $model->product_attributes()->attach($attribute, ['product_attribute_option_id' => $attributeOptionId]);
                    }
                }
            }
        } else {
            $model->productVariantAttributes()->detach();
            if ($request->product_attributes) {
                foreach ($request->product_attributes as $attributeId => $attributeOptionId) {
                    $attribute = ProductAttribute::find($attributeId);
                    if ($attribute) {
                        $model->productVariantAttributes()->attach($attribute, ['product_attribute_option_id' => $attributeOptionId]);
                    }
                }
            }
        }
    }

}
