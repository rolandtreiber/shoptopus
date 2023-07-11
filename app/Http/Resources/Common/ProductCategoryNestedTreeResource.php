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
            'parent_id' => $this->parent_id,
            'children' => ProductCategoryNestedTreeResource::collection($this->children),
        ];
    }
}
