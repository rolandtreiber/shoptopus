<?php

namespace App\Http\Requests\Cart;

use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Http\FormRequest;

class AddItemToCartRequest extends FormRequest
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
            return $user->cart->id === $cart_id;
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
            'quantity' => ['required','integer','min:1', function ($attribute, $value, $fail) {
                $productQuery =  DB::table('products')
                    ->whereNull('deleted_at')
                    ->where('id', $this->product_id);

                if (!$productQuery->exists()) {
                    $fail('Product is unavailable.');
                } else {
                    $stock = (int) $productQuery->select(['stock'])->first()['stock'];

                    if ($stock < $value) {
                        if ($stock === 0 ) {
                            $fail('Out of stock.');
                        } else if ($stock === 1) {
                            $fail('Only 1 left.');
                        } else {
                            $fail('Only ' . $stock . ' left.');
                        }
                    }
                }
            }],
            'cart_id' => 'nullable|string|exists:carts,id',
            'user_id' => 'nullable|string|exists:users,id',
            'product_variant_id' => 'sometimes|required|integer|exists:product_variants,id'
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
            'cart_id' => $this->cart_id ?: optional(optional($this->user())->cart)->id,
            'user_id' => optional($this->user())->id
        ]);
    }
}
