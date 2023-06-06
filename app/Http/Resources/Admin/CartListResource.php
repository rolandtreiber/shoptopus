<?php

namespace App\Http\Resources\Admin;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/**
 * @mixin Cart
 */
class CartListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $totalProductCount = 0;
        $totalProductOriginalValue = 0;
        $totalProductCurrentValue = 0;
        foreach ($this->products as $product) {
            $totalProductCount += $product->pivot->quantity;
            $totalProductOriginalValue += $product->pivot->quantity * $product->price;
            $totalProductCurrentValue += $product->pivot->quantity * $product->final_price;
        }

        return [
            'last_updated' => $this->updated_at ? Carbon::parse($this->updated_at)->format('Y-m-d H:i:s') : 'never',
            'products_total' => $totalProductCount,
            'products' => CartProductListResource::collection($this->products),
            'full_price' => $totalProductOriginalValue,
            'price' => $totalProductCurrentValue,
        ];
    }
}
