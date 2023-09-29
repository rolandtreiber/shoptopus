<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\Common\FileContentResource;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ProductVariant
 */
class ProductVariantListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $coverPhoto = $this->cover_image();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'product_id' => $this->product_id,
            'cover_image' => $coverPhoto?->url
        ];
    }
}
