<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {

        // Fetch valid roles from config and get the keys
        $validRoles = array_keys(config('roles'));

        return [
            'first_name' => 'required|string|min:1|max:100',
            'last_name' => 'required|string|min:1|max:100',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|max:100|confirmed',
            'phone' => 'sometimes|nullable|string|min:3|max:30',
            'role' => [
                'nullable',
                'string',
                'min:1',
                'max:100',
                Rule::in($validRoles), // Validate against the dynamic list of keys
                ]
        ];
    }

    public function messages(): array
    {
        return [
            'role.in' => 'The selected role is invalid. Allowed roles are: ' . implode(', ', array_keys(config('roles'))),
        ];
    }
}
