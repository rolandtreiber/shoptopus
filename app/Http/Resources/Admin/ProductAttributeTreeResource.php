<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductAttributeTreeResource extends JsonResource
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
            'type' => $this->type,
            'image' => $this->image,
            'options' => ProductAttributeOptionTreeResource::collection($this->options)
        ];
    }
}
