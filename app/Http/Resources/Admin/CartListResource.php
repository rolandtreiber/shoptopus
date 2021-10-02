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
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $totalProductCount = 0;
        $totalProductOriginalValue = 0;
        $totalProductCurrentValue = 0;
        foreach ($this->products as $product) {
            $totalProductCount += $product->pivot->amount;
            $totalProductOriginalValue += $product->pivot->amount * $product->price;
            $totalProductCurrentValue += $product->pivot->amount * $product->final_price;
        }

        return [
            'last_updated' => $this->updated_at ? Carbon::parse($this->updated_at)->format('Y-m-d H:i:s') : 'never',
            'products' => $totalProductCount,
            'full_price' => $totalProductOriginalValue,
            'price' => $totalProductCurrentValue
        ];
    }
}
