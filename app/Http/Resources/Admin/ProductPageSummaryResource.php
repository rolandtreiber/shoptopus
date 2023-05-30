<?php

namespace App\Http\Resources\Admin;

use App\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductPageSummaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $retailValue = Product::view('active')->get()->map(function ($product) {
            return floatval($product->final_price) * $product->stock;
        })->sum();

        return [
            'total_stock' => Product::view('active')->pluck('stock')->sum(),
            'retail_value' => $retailValue,
            'out_of_stock_items' => Product::view('active')->where('stock', 0)->count(),
            'running_low' => Product::view('active')->where('stock', '<>', 0)->where('stock', '<=', 15)->count(),
        ];
    }
}
