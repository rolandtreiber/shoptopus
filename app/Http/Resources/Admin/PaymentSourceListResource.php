<?php

namespace App\Http\Resources\Admin;

use App\Models\PaymentSource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin PaymentSource
 */
class PaymentSourceListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'exp_month' => $this->exp_month,
            'exp_year' => $this->exp_year,
            'last_four' => $this->last_four,
            'brand' => $this->brand,
            'payment_method_id' => $this->payment_method_id,
            'added' => $this->created_at,
        ];
    }
}
