<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\Common\FileContentResource;
use App\Http\Resources\Common\NoteResource;
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
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->getTranslations('name'),
            'description' => $this->getTranslations('description'),
            'short_description' => $this->getTranslations('short_description'),
            'price' => $this->price,
            'final_price' => $this->final_price,
            'status' => $this->status,
            'stock' => $this->stock,
            'purchase_count' => $this->purchase_count,
            'backup_stock' => $this->backup_stock,
            'cover_photo' => $this->cover_photo ?: null,
            'attributes' => AttributeResource::collection($this->product_attributes),
            'sku' => $this->sku,
            'tags' => ProductTagListResource::collection($this->product_tags),
            'categories' => ProductCategorySelectResource::collection($this->product_categories),
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d'),
            'updated_at' => Carbon::parse($this->updated_at)->format('Y-m-d'),
            'images' => FileContentResource::collection($this->images()),
            'pdfs' => $this->pdfs(),
            'notes' => NoteResource::collection($this->notes),
            'virtual' => $this->virtual,
            'weight' => $this->weight
        ];
    }
}
