<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\FormRequest;

/**
 * Class PasswordResetRequest
 * @package App\Http\Requests\Auth
 * @property mixed email
 */
final class PasswordResetRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'flow' => 'required',
            'email' => 'required|email'
        ];
    }

}
