<?php

namespace App\Http\Requests\Local\Cart;

use App\Models\Cart;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $product_id
 * @property string $product_variant_id
 * @property int $quantity
 * @property string $cart_id
 */
class AddItemToCartRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $cart_id = $this->cart_id;
        $user = $this->user();
        if ($cart_id && $user) {
            return $user->cart->id === $cart_id;
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
            'quantity' => Cart::quantityValidationRule($this->product_id, $this->product_variant_id, $this->cart_id,-1),
            'cart_id' => 'nullable|string|exists:carts,id',
            'user_id' => 'nullable|string|exists:users,id',
            'product_variant_id' => 'nullable|exists:product_variants,id',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'cart_id' => $this->cart_id ?: $this->user()?->cart?->id,
            'user_id' => $this->user()?->id,
        ]);
    }
}
