<?php

namespace App\Http\Resources\Admin;

use App\Models\FileContent;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Product
 * @mixin Order
 * @property FileContent|null $coverPhoto
 */
class OrderProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $productVariant = $this->pivot->product_variant_id ? new ProductVariantResource(ProductVariant::find($this->pivot->product_variant_id)) : null;

        $coverPhotoUrl = $this->coverPhoto?->url;
        if (!$coverPhotoUrl && $productVariant !==  null) {
            $coverPhotoUrl = $productVariant->images()->count() > 0 ? $productVariant->images()->first()->url : null;
        }

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
            'cover_photo_url' => $coverPhotoUrl,
        ];
    }
}
