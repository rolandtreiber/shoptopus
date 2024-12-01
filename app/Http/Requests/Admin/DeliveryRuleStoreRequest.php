<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DeliveryRuleStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'postcodes' => ['sometimes', 'array'],
            'countries' => ['sometimes', 'array'],
            'min_weight' => ['numeric', 'min:0'],
            'max_weight' => ['numeric', 'min:0'],
            'min_distance' => ['numeric', 'min:0'],
            'max_distance' => ['numeric', 'min:0'],
            'lat' => ['numeric'],
            'lon' => ['numeric'],
            'status' => ['sometimes'],
        ];
    }
}
