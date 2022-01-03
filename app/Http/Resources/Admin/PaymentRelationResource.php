<?php

namespace App\Http\Resources\Admin;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Payment
 */
class PaymentRelationResource extends JsonResource
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
            'description' => $this->description,
            'amount' => $this->amount,
            'type' => $this->type,
            'status' => $this->status,
            'payment_ref' => $this->payment_ref,
            'method_ref' => $this->method_ref,
            'crated_at' => $this->created_at,
            'source' => new PaymentSourceRelationResource($this->paymentSource)
        ];
    }
}
