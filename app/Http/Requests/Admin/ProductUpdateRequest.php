<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
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
            'name' => ['string', 'max:255'],
            'price' => ['sometimes', 'numeric'],
            'weight' => ['sometimes', 'numeric'],
            'virtual' => ['sometimes', 'boolean']
        ];
    }
}
