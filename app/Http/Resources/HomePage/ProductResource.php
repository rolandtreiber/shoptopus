<?php

namespace App\Http\Resources\HomePage;

use App\Models\FileContent;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Product
 */
class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'short_description' => $this->short_description,
            'description' => $this->description,
            'price' => (float) $this->price,
            'final_price' => (float) $this->final_price,
            'cover_photo' => $this->cover_photo,
            'images' => $this->images()->map(function(FileContent $image) {
                return [
                    'url' => $image->url,
                    'file_name' => $image->file_name
                ];
            })
        ];
    }
}
