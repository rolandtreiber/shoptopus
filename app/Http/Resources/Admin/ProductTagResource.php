<?php

namespace App\Http\Resources\Admin;

use App\Models\ProductTag;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ProductTag
 */
class ProductTagResource extends JsonResource
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
            'badge' => $this->badge,
            'updated_at' => $this->updated_at,
            'enabled' => $this->enabled,
            'description' => $this->getTranslations('description'),
            'display_badge' => $this->display_badge,
        ];
    }
}
