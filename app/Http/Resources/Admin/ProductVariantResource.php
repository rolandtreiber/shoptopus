<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'data' => $this->data,
            'description' => $this->getTranslations('description'),
            'price' => $this->price,
            'attributes' => AttributeResource::collection($this->attributes)
        ];
    }
}
