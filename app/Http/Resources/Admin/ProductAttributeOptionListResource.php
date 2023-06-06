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
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->getTranslations('name'),
            'value' => $this->value,
            'image' => $this->image ? $this->image->url : null,
            'enabled' => $this->enabled,
        ];
    }
}
