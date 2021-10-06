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
        return [
            'id' => $this->id,
            'avatar' => $this->avatar,
            'name' => $this->name,
            'prefix' => $this->prefix,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'initials' => $this->initials,
            'email' => $this->email,
            'email_verified' => $this->email_verified_at !== null,
            'addresses' => AddressResource::collection($this->addresses),
            'orders' => OrderListResource::collection($this->orders),
            'payments' => OrderListResource::collection($this->payments),
            'payment_sources' => OrderListResource::collection($this->paymentSources),
            'cart' => new CartListResource($this->cart)
        ];
    }
}
