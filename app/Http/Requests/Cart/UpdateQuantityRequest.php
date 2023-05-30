<?php

namespace App\Http\Requests\Cart;

use App\Models\Cart;
use Illuminate\Foundation\Http\FormRequest;

class UpdateQuantityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if ($user = $this->user()) {
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
            'quantity' => Cart::quantityValidationRule($this->product_id),
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
            'user_id' => $this->user()?->id,
            'cart_id' => $this->cart_id,
            'product_id' => $this->product_id,
        ]);
    }
}
