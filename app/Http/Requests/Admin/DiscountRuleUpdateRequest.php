<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DiscountRuleUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => ['numeric'],
            'name' => ['json'],
            'amount' => ['numeric'],
            'valid_from' => ['date'],
            'valid_until' => ['date']
        ];
    }
}
