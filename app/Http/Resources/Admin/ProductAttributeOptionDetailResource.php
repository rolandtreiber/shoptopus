<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\Common\NoteResource;
use App\Models\ProductAttributeOption;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ProductAttributeOption
 */
class ProductAttributeOptionDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->getTranslations('name'),
            'product_attribute_id' => $this->product_attribute_id,
            'value' => $this->value,
            'image' => $this->image ? $this->image->url : null,
            'enabled' => $this->enabled,
            'notes' => NoteResource::collection($this->notes),
        ];
    }
}
