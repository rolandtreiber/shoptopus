<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'json'],
            'price' => ['required', 'numeric'],
            'short_description' => ['required', 'json'],
            'description' => ['required', 'json'],
            'stock' => ['sometimes', 'numeric'],
            'backup_stock' => ['sometimes', 'numeric'],
            'product_attributes' => ['sometimes', 'array'],
        ];
    }
}
