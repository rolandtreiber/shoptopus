<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\Common\AddressResource;
use App\Models\Cart;
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
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
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
            'orders' => OrderListResource::collection($this->orders()->orderByDesc('created_at')->get()),
            'payments' => PaymentRelationResource::collection($this->payments),
            'payment_sources' => PaymentSourceListResource::collection($this->payment_sources),
            'cart' => new CartListResource($this->cart),
            'total_orders' => $this->orders()->count(),
            'total_spent' => $this->payments()->sum('amount'),
            'latest_order_date' => $latestOrderDate?->format('d-m-Y H:i')
        ];
    }
}
