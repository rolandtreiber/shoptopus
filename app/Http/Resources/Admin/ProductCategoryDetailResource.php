<?php

namespace App\Http\Resources\Admin;

use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $menu_image
 * @property mixed $header_image
 */
class ProductCategoryDetailResource extends JsonResource
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
            'menu_image' => $this->menu_image,
            'header_image' => $this->header_image,
            'parent' => new ProductCategoryListResource($this->parent)
        ];
    }
}
