<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $menu_image
 * @property mixed $header_image
 * @property mixed $parent
 */
class ProductCategoryListResource extends JsonResource
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
            'description' => $this->getTranslations('description'),
            'menu_image' => $this->menu_image ? $this->menu_image->url : null,
            'header_image' => $this->header_image ? $this->header_image->url : null,
            'children' => ProductCategoryListResource::collection($this->children)
        ];
    }
}
