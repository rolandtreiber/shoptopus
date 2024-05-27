<?php

namespace App\Http\Requests\Local\Cart;

use Illuminate\Foundation\Http\FormRequest;

class RemoveItemFromCartRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'product_id' => 'bail|required|string|exists:products,id',
            'cart_id' => 'required|string|exists:carts,id',
            'user_id' => 'nullable|string|exists:users,id',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'user_id' => $this->user()?->id,
        ]);
    }
}
