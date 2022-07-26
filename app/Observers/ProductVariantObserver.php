<?php

namespace App\Observers;

use App\Models\ProductVariant;

class ProductVariantObserver
{
    public function updated(ProductVariant $productVariant)
    {
        $productVariant->product->recalculateStock();
    }

    public function deleted(ProductVariant $productVariant)
    {
        $productVariant->product->recalculateStock();
    }
}
