<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\FormRequest;

/**
 * Class PasswordResetTokenCheckRequest
 * @package App\Http\Requests\Auth
 */
final class PasswordResetTokenCheckRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'password_reset_token' => 'required'
        ];
    }

}
