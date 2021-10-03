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
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'type' => $this->type,
            'status' => $this->status,
            'crated_at' => $this->created_at,
            'payable_id' => $this->payable_id,
            'payable_type' => $this->payable_type,
            'user' => new UserListResource($this->user)
        ];
    }
}
