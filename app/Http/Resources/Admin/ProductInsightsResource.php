<?php

namespace App\Http\Resources\Admin;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductInsightsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Product $product */
        $product = $request['product'];

        $latestOrders = $product->orders()->orderByDesc('created_at')->limit(5)->get()->map(function($order) {
            return [
                'user' => [
                    'id' => $order['user']['id'],
                    'name' => $order['user']['name']
                ],
                'placed_at' => $order['created_at'],
                'amount' => $order['pivot']['amount'],
                'status' => $order['status'],
                'order_total' => $order['pivot']['final_price']
            ];
        });

        return [
            'overall_satisfaction' => $this['overall_satisfaction'],
            'latest_orders' => $latestOrders,
            'sales_timeline' => $this['sales_timeline']
        ];
    }
}
