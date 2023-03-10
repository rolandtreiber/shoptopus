<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DeliveryTypeUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => ['json'],
            'description' => ['json'],
            'status' => ['sometimes', 'numeric'],
            'price' => ['numeric'],
        ];
    }
}
