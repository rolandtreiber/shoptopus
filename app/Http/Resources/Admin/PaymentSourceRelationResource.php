<?php

namespace App\Http\Resources\Admin;

use App\Models\PaymentSource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin PaymentSource
 */
class PaymentSourceRelationResource extends JsonResource
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
            'payment_method_id' => $this->payment_method_id,
        ];
    }
}
