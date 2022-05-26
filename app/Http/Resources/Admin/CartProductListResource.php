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
    public function toArray($request)
    {
        $totalOriginalPrice = $this->price * $this->pivot->quantity;
        $totalFinalPrice = $this->final_price * $this->pivot->quantity;
        $totalDiscount = $totalOriginalPrice - $totalFinalPrice;

        return [
            'name' => $this->getTranslations('name'),
            'quantity' => $this->pivot->quantity,
            'price' => $totalOriginalPrice,
            'discount' => $totalDiscount,
            'final_price' => $totalFinalPrice,
            'variant' => ProductVariantResource::make(ProductVariant::find($this->pivot->product_variant_id))
        ];
    }
}
