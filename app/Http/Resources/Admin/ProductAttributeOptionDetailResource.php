<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\Common\NoteResource;
use App\Models\ProductAttributeOption;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ProductAttributeOption
 */
class ProductAttributeOptionDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $variantsGrouped = [];

        /** @var ProductVariant $productVariant */
        foreach ($this->product_variants as $productVariant) {
            if (array_key_exists($productVariant->product_id, $variantsGrouped)) {
                $variantsGrouped[$productVariant->product_id]['variants'][] = new ProductVariantListResource($productVariant);
            } else {
                $variantsGrouped[$productVariant->product_id]['product'] = new ProductListResource($productVariant->product);
                $variantsGrouped[$productVariant->product_id]['variants'][] = new ProductVariantListResource($productVariant);
            }
        }

        return [
            'id' => $this->id,
            'name' => $this->getTranslations('name'),
            'product_attribute_id' => $this->product_attribute_id,
            'value' => $this->value,
            'image' => $this->image ? $this->image->url : null,
            'enabled' => $this->enabled,
            'notes' => NoteResource::collection($this->notes),
            'associated_products' => ProductListResource::collection($this->products),
            'associated_product_variants' => $variantsGrouped
        ];
    }
}
