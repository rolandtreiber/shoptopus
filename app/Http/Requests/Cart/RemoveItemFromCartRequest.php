<?php

namespace App\Http\Requests\Cart;

use Illuminate\Foundation\Http\FormRequest;

class RemoveItemFromCartRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() : bool
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
     * @return array
     */
    public function rules() : array
    {
        return [
            'product_id'  => 'bail|required|string|exists:products,id',
            'cart_id' => 'required|string|exists:carts,id',
            'user_id' => 'nullable|string|exists:users,id'
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'user_id' => optional($this->user())->id
        ]);
    }
}
