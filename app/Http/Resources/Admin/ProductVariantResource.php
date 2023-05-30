<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\Common\FileContentResource;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/**
 * @mixin ProductVariant
 */
class ProductVariantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'stock' => $this->stock,
            'description' => $this->getTranslations('description'),
            'price' => $this->price,
            'attributes' => AttributeResource::collection($this->product_variant_attributes),
            'images' => FileContentResource::collection($this->fileContents),
            'created_at' => Carbon::parse($this->create_at)->format('Y-m-d'),
            'updated_at' => Carbon::parse($this->updated_at)->format('Y-m-d'),
            'image' => $this->cover_image(),
            'sku' => $this->sku,
            'enabled' => $this->enabled,
        ];
    }
}
