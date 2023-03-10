<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:4|max:100',
            'cart_id' => 'sometimes|nullable|string|exists:carts,id',
        ];
    }

//    public function messages(): array
//    {
//        return [
//            'email.required' => 'Email is required!',
//            'email.email' => 'The email is invalid',
//            'password.required' => 'Password is required!'
//        ];
//    }
}
