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
        $finalPrice = $product->final_price;
        $fullPrice = $product->price;

        $variant = null;
        if ($orderProduct->product_variant_id) {
            $variant = ProductVariant::find($variant);
            if ($product->price !== $variant->price) {
                $finalPrice = $variant->final_price;
                $fullPrice = $variant->price;
            }
        }

        $orderProduct->name = $product->getTranslations('name');
        $orderProduct->unit_price = $finalPrice;
        $orderProduct->full_price = round($fullPrice * $orderProduct->amount, 2);
        $orderProduct->final_price = round($finalPrice * $orderProduct->amount, 2);
        $orderProduct->original_unit_price = round($product->price, 2);
        $orderProduct->unit_discount = round($product->price - $finalPrice, 2);
        $orderProduct->total_discount = round(($fullPrice * $orderProduct->amount) - ($finalPrice * $orderProduct->amount), 2);
    }

    public function saved(OrderProduct $orderProduct)
    {
        $order = $orderProduct->order;
        $order->recalculatePrices();
    }
}
