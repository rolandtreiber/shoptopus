<?php

namespace App\Observers;

use App\Models\ProductAttributeOption;
use App\Traits\ProcessRequest;

class ProductAttributeOptionObserver
{
    use ProcessRequest;

    public function deleting(ProductAttributeOption $productAttributeOption): void
    {
        $productAttributeOption->image && $this->deleteCurrentFile($productAttributeOption->image->file_name);
    }
}
