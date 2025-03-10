<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DiscountRuleStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'numeric'],
            'name' => ['required', 'json'],
            'amount' => ['required', 'numeric'],
            'valid_from' => ['required', 'date'],
            'valid_until' => ['required', 'date'],
            'enabled' => ['required'],
        ];
    }
}
