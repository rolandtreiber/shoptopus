<?php

namespace App\Observers;

use App\Models\ProductVariant;

class ProductVariantObserver
{
    public function saved(ProductVariant $productVariant)
    {
        $productVariant->product->recalculateStock();
    }

    public function deleted(ProductVariant $productVariant): void
    {
        $productVariant->product->recalculateStock();
    }
}
