<?php

namespace App\Observers;

use App\Enums\ProductAttributeType;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeOption;
use App\Traits\ProcessRequest;
use Illuminate\Support\Facades\Log;

class ProductAttributeObserver
{
    use ProcessRequest;

    public function saving(ProductAttribute $productAttribute): void
    {
        if ($productAttribute->isDirty('type') && (int) $productAttribute->type === ProductAttributeType::Color) {
            $attributeOptions = $productAttribute->options;
            $attributeOptions->map(function (ProductAttributeOption $option) {
                $option->value = "#ffffff";
                $option->save();
            });
        }
    }
    public function deleting(ProductAttribute $productAttribute): void
    {
        $productAttribute->image && $this->deleteCurrentFile($productAttribute->image->file_name);
        $productAttribute->image && $this->deleteCurrentFile($productAttribute->image->file_name);
        $options = $productAttribute->options;
        foreach ($options as $option) {
            $option->delete();
        }
    }
}
