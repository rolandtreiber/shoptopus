<?php

namespace App\Http\Resources\Admin;

use App\Models\ProductAttribute;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ProductAttribute
 */
class ProductVariantResource extends JsonResource
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
            'description' => $this->getTranslations('description'),
            'price' => $this->price,
            'attributes' => AttributeResource::collection($this->attributes)
        ];
    }
}
