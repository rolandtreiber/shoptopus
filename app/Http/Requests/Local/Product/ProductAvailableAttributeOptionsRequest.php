<?php

namespace App\Http\Requests\Local\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductAvailableAttributeOptionsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'selected_attribute_options' => ['array'],
        ];
    }
}
