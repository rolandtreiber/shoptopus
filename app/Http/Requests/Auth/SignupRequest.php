<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\FormRequest;

/**
 * Class SignupRequest
 * @package App\Http\Requests\Auth
 * @property string $email
 * @property string $name
 * @property string $password
 */
final class SignupRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'name' => 'Name is required!',
            'email.required' => 'Email is required!',
            'email.email' => 'The email is invalid',
            'password.required' => 'Password is required!'
        ];
    }

}
