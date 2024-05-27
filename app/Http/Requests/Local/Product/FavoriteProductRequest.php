<?php

namespace App\Http\Requests\Local\Product;

use Illuminate\Foundation\Http\FormRequest;

class FavoriteProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'productId' => 'required|string|exists:products,id',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'productId' => $this->route('id'),
        ]);
    }
}
