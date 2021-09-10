<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\FormRequest;

/**
 * Class LoginRequest
 * @package App\Http\Requests\Auth
 * @property mixed email
 * @property mixed password
 */
final class LoginRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email is required!',
            'email.email' => 'The email is invalid',
            'password.required' => 'Password is required!'
        ];
    }
}
