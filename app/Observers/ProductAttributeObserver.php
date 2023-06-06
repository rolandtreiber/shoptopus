<?php

namespace App\Observers;

use App\Models\ProductAttribute;
use App\Traits\ProcessRequest;

class ProductAttributeObserver
{
    use ProcessRequest;

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
