<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\Common\NoteResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductTagDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->getTranslations('name'),
            'badge' => $this->badge ? $this->badge->url : null,
            'updated_at' => $this->updated_at,
            'enabled' => $this->enabled,
            'description' => $this->getTranslations('description'),
            'display_badge' => $this->display_badge,
            'products' => ProductListResource::collection($this->products),
            'notes' => NoteResource::collection($this->notes),
        ];
    }
}
