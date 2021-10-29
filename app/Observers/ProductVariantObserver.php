<?php

namespace App\Observers;

use App\Models\ProductVariant;

class ProductVariantObserver
{

    public function saved(ProductVariant $productVariant)
    {
        $productVariant->updateParentStock();
    }

    public function deleted(ProductVariant $productVariant)
    {
        $productVariant->updateParentStock();
    }

}
