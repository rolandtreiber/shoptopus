<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\FormRequest;

/**
 * Class PasswordResetTokenCheckRequest
 */
final class PasswordResetTokenCheckRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'password_reset_token' => 'required',
        ];
    }
}
