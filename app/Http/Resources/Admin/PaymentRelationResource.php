<?php

namespace App\Http\Resources\Admin;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/**
 * @mixin Payment
 */
class PaymentRelationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'amount' => $this->amount,
            'type' => $this->type,
            'status' => $this->status,
            'payment_ref' => $this->payment_ref,
            'method_ref' => $this->method_ref,
            'payment_method' => $this->payment_source->payment_method_id,
            'brand' => $this->payment_source->brand,
            'last_four' => $this->payment_source->last_four,
            'exp_month' => $this->payment_source->exp_month,
            'exp_year' => $this->payment_source->exp_year,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
            'source' => new PaymentSourceRelationResource($this->payment_source),
        ];
    }
}
