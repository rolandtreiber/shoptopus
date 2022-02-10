<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductAttributeOptionTreeResource extends JsonResource
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
            'id' =>$this->id,
            'name' => $this->getTranslations('name'),
            'value' => $this->common_value,
            'image' => $this->image,
            'product_attribute_id' => $this->product_attribute_id
        ];
    }
}
