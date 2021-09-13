<?php

namespace App\Observers;

use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\ProductVariant;

class OrderProductObserver
{
    public function saving(OrderProduct $orderProduct)
    {
        $product = Product::find($orderProduct->product_id);
        $price = $product->final_price;

        $variant = null;
        if ($orderProduct->product_variant_id) {
            $variant = ProductVariant::find($variant);
            if ($product->price !== $variant->price) {
                $price = $variant->price;
            }
        }

        $orderProduct->name = $product->getTranslations('name');
        $orderProduct->unit_price = $price;
        $orderProduct->price = round($price * $orderProduct->amount, 2);
    }
}
