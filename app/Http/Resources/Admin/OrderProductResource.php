<?php

namespace App\Http\Resources\Admin;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Product
 * @mixin Order
 */
class OrderProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->pivot->name,
            'variant' => $this->pivot->product_variant_id ? new ProductVariantResource(ProductVariant::find($this->pivot->product_variant_id)) : null,
            'amount' => $this->pivot->amount,
            'unit_price' => $this->pivot->unit_price,
            'final_price' => $this->pivot->final_price,
        ];
    }
}
