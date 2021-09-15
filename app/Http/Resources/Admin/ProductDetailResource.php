<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailResource extends JsonResource
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
            'name' => $this->getTranslations('name'),
            'description' =>$this->getTranslations('description'),
            'short_description' => $this->getTranslations('short_description'),
            'price' => $this->price,
            'status' => $this->status,
            'stock' => $this->stock,
            'purchase_count' => $this->purchase_count,
            'backup_stock' => $this->backup_stock,
            'final_price' => $this->final_price,
            'attributes' => AttributeResource::collection($this->attributes),
            'variants' => ProductVariantResource::collection($this->productVariants()->with('attributes')->get()),
            'tags' => $this->tags,
            'categories' => $this->categories
        ];
    }
}
