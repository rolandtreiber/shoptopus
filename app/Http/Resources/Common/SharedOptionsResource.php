<?php

namespace App\Http\Resources\Common;

use App\Enums\AvailabilityStatuses;
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
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $categories = ProductCategoryNestedTreeResource::collection(ProductCategory::availability(AvailabilityStatuses::Enabled)->whereNull('parent_id')->select('id', 'name')->with('children.children.children.children.children.children')->get());
        $tags = ProductTagSelectResource::collection(ProductTag::availability(AvailabilityStatuses::Enabled)->select('id', 'name')->get());
        $attributes = ProductAttributeTreeResource::collection(ProductAttribute::availability(AvailabilityStatuses::Enabled)->select('id', 'name', 'type', 'image')->with('options')->get());
        return [
            'categories' => $categories,
            'tags' => $tags,
            'attributes' => $attributes
        ];
    }
}
