<?php

namespace App\Http\Resources\Admin;

use App\Models\ProductAttributeOption;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ProductAttributeOption
 */
class ProductAttributeOptionListResource extends JsonResource
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
            'common_value' => $this->common_value,
            'image' => $this->image ? $this->image->url : null,
            'enabled' => $this->enabled,
        ];
    }
}
