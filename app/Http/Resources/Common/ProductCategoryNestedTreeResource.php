<?php

namespace App\Http\Resources\Common;

use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ProductCategory
 */
class ProductCategoryNestedTreeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->getTranslations('name'),
            'menu_image' => $this->menu_image,
            'header_image' => $this->header_image,
            'slug' => $this->slug,
            'parent_id' => $this->parent_id,
            'subcategories' => ProductCategoryNestedTreeResource::collection($this->children),
        ];
    }
}
