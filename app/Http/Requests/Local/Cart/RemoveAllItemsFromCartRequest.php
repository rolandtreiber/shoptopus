<?php

namespace App\Http\Requests\Local\Cart;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $cart_id
 */
class RemoveAllItemsFromCartRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $cart_id = $this->cart_id;
        $user = $this->user();

        if ($cart_id && $user) {
            return $user->cart->id === $this->cart_id;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'cart_id' => 'required|string|exists:carts,id',
        ];
    }
}
