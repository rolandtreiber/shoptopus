<?php

namespace App\Http\Requests\Local\Product;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateReviewRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'token' => ['required', 'exists:access_tokens,token'],
            'rating' => ['required', 'numeric', 'min:1', 'max:5'],
            'title' => ['required'],
            'description' => ['required']
        ];
    }
}
