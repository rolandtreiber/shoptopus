<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\FormRequest;

/**
 * Class EmailConfirmationRequest
 *
 * @property mixed $email_confirmation_token
 */
final class EmailConfirmationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email_confirmation_token' => 'required',
        ];
    }
}
