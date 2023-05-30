<?php

namespace App\Http\Resources\Admin;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Product
 */
class CartProductListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        if ($this->pivot->product_variant_id) {
            /** @var ProductVariant $variant */
            $variant = ProductVariant::find($this->pivot->product_variant_id);
            $totalOriginalPrice = round($variant->price * $this->pivot->quantity, 2);
            $totalFinalPrice = round($variant->final_price * $this->pivot->quantity, 2);
            $totalDiscount = round($totalOriginalPrice - $totalFinalPrice, 2);
        } else {
            $totalOriginalPrice = round($this->price * $this->pivot->quantity, 2);
            $totalFinalPrice = round($this->final_price * $this->pivot->quantity, 2);
            $totalDiscount = round($totalOriginalPrice - $totalFinalPrice, 2);
        }

        return [
            'name' => $this->getTranslations('name'),
            'quantity' => $this->pivot->quantity,
            'price' => $totalOriginalPrice,
            'discount' => $totalDiscount,
            'final_price' => $totalFinalPrice,
            'item_full_price' => round($this->price, 2),
            'item_final_price' => round($this->final_price, 2),
            'variant' => ProductVariantListResource::make(ProductVariant::find($this->pivot->product_variant_id)),
        ];
    }
}
