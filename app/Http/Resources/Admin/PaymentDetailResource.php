<?php

namespace App\Http\Resources\Admin;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Payment
 */
class PaymentDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'amount' => $this->amount,
            'slug' => $this->slug,
            'payment_ref' => $this->payment_ref,
            'method_ref' => $this->method_ref,
            'type' => $this->type,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'payable_id' => $this->payable_id,
            'payable_type' => $this->payable_type,
            'user' => new UserListResource($this->user),
            'payment_source' => new PaymentSourceListResource($this->payment_source),
            'payable' => $this->payable,
        ];
    }
}
