<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\Common\FileContentResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/**
 * @mixin Product
 */
class ProductDetailResource extends JsonResource
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
            'description' =>$this->getTranslations('description'),
            'short_description' => $this->getTranslations('short_description'),
            'price' => $this->price,
            'final_price' => $this->final_price,
            'status' => $this->status,
            'stock' => $this->stock,
            'purchase_count' => $this->purchase_count,
            'backup_stock' => $this->backup_stock,
            'cover_photo' => new FileContentResource($this->coverPhoto),
            'attributes' => AttributeResource::collection($this->productAttributes),
            'sku' => $this->sku,
            'tags' => ProductTagListResource::collection($this->tags),
            'categories' => ProductCategorySelectResource::collection($this->categories),
            'created_at' => Carbon::parse($this->create_at)->format('Y-m-d'),
            'updated_at' => Carbon::parse($this->updated_at)->format('Y-m-d'),
            'images' => FileContentResource::collection($this->images()),
            'pdfs' => $this->pdfs()
        ];
    }
}
