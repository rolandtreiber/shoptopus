<?php

namespace App\Http\Resources\Admin;

use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ProductCategory
 */
class ProductCategoryDetailResource extends JsonResource
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
            'description' => $this->getTranslations('description'),
            'menu_image' => $this->menu_image ? $this->menu_image->url : null,
            'header_image' => $this->header_image ? $this->header_image->url : null,
            'parent_id' => $this->parent_id,
            'tree' => $this->tree(),
            'children' => ProductCategoryListResource::collection($this->children),
            'products' => ProductListResource::collection($this->products()->get()),
            'enabled' => $this->enabled,
        ];
    }
}
