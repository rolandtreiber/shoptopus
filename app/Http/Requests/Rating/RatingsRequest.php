<?php

namespace App\Http\Requests\Rating;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RatingsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'language_prefix' => ['required'],
            'ratings.*' => ['min:1', 'max:5']
        ];
    }
}
