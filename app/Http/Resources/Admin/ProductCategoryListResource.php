<?php

namespace App\Http\Resources\Admin;

use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ProductCategory
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
            'children' => ProductCategoryListResource::collection($this->children),
            'enabled' => $this->enabled,
            'updated_at' => $this->updated_at
        ];
    }
}
