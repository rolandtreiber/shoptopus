<?php

namespace App\Http\Resources\Common;

use App\Enums\AvailabilityStatus;
use App\Http\Resources\Admin\ProductAttributeTreeResource;
use App\Http\Resources\Admin\ProductTagSelectResource;
use App\Models\ProductAttribute;
use App\Models\ProductCategory;
use App\Models\ProductTag;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SharedOptionsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $categories = ProductCategoryNestedTreeResource::collection(ProductCategory::availability(AvailabilityStatus::Enabled)->whereNull('parent_id')->select('id', 'name')->with('children.children.children.children.children.children')->get());
        $tags = ProductTagSelectResource::collection(ProductTag::availability(AvailabilityStatus::Enabled)->select('id', 'name')->get());
        $attributes = ProductAttributeTreeResource::collection(ProductAttribute::availability(AvailabilityStatus::Enabled)->select('id', 'name', 'type', 'image')->with('options')->get());

        return [
            'categories' => $categories,
            'tags' => $tags,
            'attributes' => $attributes,
        ];
    }
}
