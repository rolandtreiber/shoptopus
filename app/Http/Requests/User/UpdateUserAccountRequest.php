<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserAccountRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'prefix' => ['sometimes', 'in:'.implode(",", config('users.available_prefixes'))],
            'password' => ['sometimes', 'confirmed', Password::min(5)->mixedCase()->numbers()->symbols()]
        ];
    }

    public function messages()
    {
        return [
            'in' => "The prefix needs to be one of the following: ".implode(",", config('users.available_prefixes'))
        ];
    }
}
