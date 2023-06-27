<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\Common\AddressResource;
use App\Http\Resources\Common\NoteResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class CustomerDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $latestOrderDate = null;
        if ($this->orders->count() > 0) {
            $latestOrder = $this->orders()->orderByDesc('created_at')->first();
            $latestOrderDate = $latestOrder->created_at;
        }

        return [
            'id' => $this->id,
            'avatar' => $this->avatar,
            'name' => $this->name,
            'prefix' => $this->prefix,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'initials' => $this->initials,
            'email' => $this->email,
            'phone' => $this->phone,
            'email_verified' => $this->email_verified_at !== null,
            'address' => AddressResource::collection($this->addresses),
            'orders' => OrderListResource::collection($this->latest_orders),
            'payments' => PaymentRelationResource::collection($this->latest_payments),
            'payment_sources' => PaymentSourceListResource::collection($this->payment_sources),
            'cart' => new CartListResource($this->cart),
            'total_orders' => $this->orders()->count(),
            'total_spent' => $this->payments()->sum('amount'),
            'latest_order_date' => $latestOrderDate?->format('Y-m-d H:i:s'),
            'notes' => NoteResource::collection($this->notes),
            'cart_item_count' => $this->cart->count(),
            'last_seen' => $this->last_seen?->format('Y-m-d H:i:s'),
            'ratings' => RatingListResource::collection($this->ratings)
        ];
    }
}
