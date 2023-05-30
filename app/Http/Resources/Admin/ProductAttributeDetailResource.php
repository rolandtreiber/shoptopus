<?php

namespace App\Http\Resources\Admin;

use App\Models\ProductAttribute;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ProductAttribute
 */
class ProductAttributeDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->getTranslations('name'),
            'type' => $this->type,
            'image' => $this->image ? $this->image->url : null,
            'options' => ProductAttributeOptionListResource::collection($this->options),
            'enabled' => $this->enabled,
        ];
    }
}
