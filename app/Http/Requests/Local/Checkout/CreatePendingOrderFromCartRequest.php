<?php

namespace App\Http\Requests\Local\Checkout;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $cart_id
 * @property string $address_id
 * @property string $delivery_type_id
 * @property string[] $address
 */
class CreatePendingOrderFromCartRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        $cart_id = $this->cart_id;

        if ($cart_id && $user) {
            return $user->cart->id === $cart_id;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'guest_checkout' => 'sometimes|boolean',
            'user' => 'nullable|array',
            'cart_id' => 'required|string|exists:carts,id',
            'address_id' => 'nullable|string|exists:addresses,id',
            'address' => 'nullable|array',
            'delivery_type_id' => 'nullable|string|exists:delivery_types,id',
        ];
    }
}
