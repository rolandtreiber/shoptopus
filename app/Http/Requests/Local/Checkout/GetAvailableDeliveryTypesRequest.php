<?php

namespace App\Http\Requests\Local\Checkout;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class GetAvailableDeliveryTypesRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'cart_id' => 'required|exists:carts,id',
            'address_id' => 'nullable|exists:addresses,id',
            'address' => 'nullable|array'
        ];
    }
}
