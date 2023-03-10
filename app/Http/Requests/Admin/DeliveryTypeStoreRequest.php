<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DeliveryTypeStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'json'],
            'description' => ['required', 'json'],
            'status' => ['sometimes', 'numeric'],
            'price' => ['required', 'numeric'],
        ];
    }
}
