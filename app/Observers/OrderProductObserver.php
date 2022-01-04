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

        if ($orderProduct->product_variant_id) {
            $variant = ProductVariant::find($orderProduct->product_variant_id);
            if ($product->price !== $variant->price) {
                $finalPrice = $variant->final_price;
                $fullPrice = $variant->price;
            } else {
                $finalPrice = $product->final_price;
                $fullPrice = $product->price;
            }
            $orderProduct->name = $variant->name;
        } else {
            $finalPrice = $product->final_price;
            $fullPrice = $product->price;
            $orderProduct->name = $product->attributedTranslatedName;
        }

        $orderProduct->full_price = round($fullPrice * $orderProduct->amount, 2);
        $orderProduct->final_price = round($finalPrice * $orderProduct->amount, 2);

        $orderProduct->unit_price = $finalPrice;
        $orderProduct->original_unit_price = round($fullPrice, 2);

        $orderProduct->unit_discount = round($fullPrice - $finalPrice, 2);
        $orderProduct->total_discount = round(($fullPrice * $orderProduct->amount) - ($finalPrice * $orderProduct->amount), 2);
    }

    public function saved(OrderProduct $orderProduct)
    {
        $order = $orderProduct->order;
        $order->recalculatePrices();
    }
}
