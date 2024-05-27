<?php

namespace App\Traits;

use App\Models\Product;
use App\Models\ProductAttribute;

trait HasAttributes
{
    public function handleAttributes($model, $request)
    {
        if ($request->has('product_attributes')) {
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
                $model->updateAvailableAttributeOptions();
            } else {
                $model->product_variant_attributes()->detach();
                if ($request->product_attributes) {
                    foreach ($request->product_attributes as $attributeId => $attributeOptionId) {
                        $attribute = ProductAttribute::find($attributeId);
                        if ($attribute) {
                            $model->product_variant_attributes()->attach($attribute, ['product_attribute_option_id' => $attributeOptionId]);
                        }
                    }
                }
                $model->product->updateAvailableAttributeOptions();
            }
        }
    }
}
