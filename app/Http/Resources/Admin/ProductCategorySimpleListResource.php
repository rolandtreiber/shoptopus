<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductCategorySimpleListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->getTranslations('name'),
            'description' => $this->getTranslations('description'),
            'menu_image' => $this->menu_image ? $this->menu_image->url : null,
            'header_image' => $this->header_image ? $this->header_image->url : null,
            'enabled' => $this->enabled,
            'updated_at' => $this->updated_at,
        ];
    }
}
