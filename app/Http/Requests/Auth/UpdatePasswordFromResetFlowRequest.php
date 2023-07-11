<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\FormRequest;

/**
 * Class UpdatePasswordFromResetFlowRequest
 *
 * @property string $password
 * @property string $password_reset_token
 */
final class UpdatePasswordFromResetFlowRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'password' => 'required',
            'password_reset_token' => 'required',
        ];
    }
}
