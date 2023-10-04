<?php

namespace App\Observers;

use App\Enums\AccessTokenType;
use App\Models\AccessToken;
use App\Models\OrderProduct;
use App\Models\PaidFileContent;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Carbon;

class OrderProductObserver
{
    /**
     * @param OrderProduct $orderProduct
     * @return void
     */
    public function creating(OrderProduct $orderProduct): void
    {
        $orderProduct->created_at = $orderProduct->order->created_at;
        $orderProduct->updated_at = Carbon::now();
    }

    /**
     * @param OrderProduct $orderProduct
     * @return void
     */
    public function saving(OrderProduct $orderProduct): void
    {
        /** @var Product $product */
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

        $orderProduct->updated_at = Carbon::now();
        $orderProduct->full_price = round($fullPrice * $orderProduct->amount, 2);
        $orderProduct->final_price = round($finalPrice * $orderProduct->amount, 2);

        $orderProduct->unit_price = $finalPrice;
        $orderProduct->original_unit_price = round($fullPrice, 2);

        $orderProduct->unit_discount = round($fullPrice - $finalPrice, 2);
        $orderProduct->total_discount = round(($fullPrice * $orderProduct->amount) - ($finalPrice * $orderProduct->amount), 2);

        $orderUserId = $orderProduct->order->user->id;
        if ($product->virtual === true) {
            $urls = [];
            $paidFileContents = $product->paidFileContents;
            foreach ($paidFileContents as $paidFileContent) {
                $accessToken = new AccessToken();
                $accessToken->accessable_id = $paidFileContent->id;
                $accessToken->accessable_type = PaidFileContent::class;
                $accessToken->user_id = $orderUserId;
                $accessToken->issuer_user_id = $orderUserId;
                $accessToken->expiry = Carbon::now()->addYears(10);
                $accessToken->type = AccessTokenType::PaidFileAccess;
                $accessToken->save();
                $urls[] = config('app.url').'/api/download-paid-file/'.$paidFileContent->id.'?token=' . $accessToken->token;
            }
            $orderProduct->urls = $urls;
        }

    }

    /**
     * @param OrderProduct $orderProduct
     * @return void
     */
    public function saved(OrderProduct $orderProduct): void
    {
        $order = $orderProduct->order;
        $order->recalculatePrices();
    }
}
