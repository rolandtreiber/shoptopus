<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\Common\FileContentResource;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ProductVariant
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
            'stock' => $this->stock,
            'description' => $this->getTranslations('description'),
            'price' => $this->price,
            'attributes' => AttributeResource::collection($this->attributes),
            'images' => FileContentResource::collection($this->fileContents),
        ];
    }
}
