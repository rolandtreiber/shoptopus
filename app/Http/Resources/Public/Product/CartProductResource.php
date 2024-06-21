<?php

namespace App\Http\Resources\Public\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $price = $this->price;
        $finalPrice = $this->final_price;
        $quantity = $this->pivot->quantity;

        return [
            'id' => $this->id,
            'product_id' => $this->pivot->product_id,
            'product_variant_id' => $this->pivot->product_variant_id,
            'name' => $this->pivot->name,
            'item_original_price' => round((float) $price, 2),
            'item_final_price' =>  round((float) $finalPrice, 2),
            'subtotal_original_price' => round((float) $price, 2) * $quantity,
            'subtotal_final_price' => round((float) $finalPrice, 2) * $quantity,
            'quantity' => $quantity,
            'remaining_stock' => $this->pivot->remaining_stock,
            'in_other_carts' => $this->pivot->inOtherCarts
        ];
    }
}
