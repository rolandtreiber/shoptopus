<?php

namespace App\Http\Resources\Admin;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Product
 */
class ProductListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->getTranslations('name'),
            'price' => $this->price,
            'final_price' => $this->final_price,
            'cover_photo_url' => $this->cover_photo ? $this->cover_photo->url : null,
            'status' => $this->status,
            'stock' => $this->stock,
            'variants' => $this->product_variants ? $this->product_variants()->count() : [],
            'updated_at' => $this->updated_at,
        ];
    }
}
