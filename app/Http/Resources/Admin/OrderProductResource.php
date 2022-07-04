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
        /** @var Product $product */
        $product = $this->pivot;
        $productVariant = $this->pivot->product_variant_id ? new ProductVariantResource(ProductVariant::find($this->pivot->product_variant_id)) : null;
        $productNameTranslations = $product->getTranslations('name');

        return [
            'id' => $this->pivot->id,
            'product_id' => $this->pivot->product_id,
            'name' => $this->pivot->getTranslations('name'),
            'variant' => $productVariant,
            'sku' => $productVariant ? $productVariant->sku : $this->sku,
            'amount' => $this->pivot->amount,
            'original_unit_price' => round((float) $this->pivot->original_unit_price, 2),
            'unit_price' => round((float) $this->pivot->unit_price, 2),
            'full_price' => round((float) $this->pivot->full_price, 2),
            'final_price' => round((float) $this->pivot->final_price, 2),
            'unit_discount' => round((float) $this->pivot->unit_discount, 2),
            'total_discount' => round((float) $this->pivot->total_discount, 2),
            'cover_photo_url' => $this->coverPhoto ? $this->coverPhoto->url : null,
        ];
    }
}
