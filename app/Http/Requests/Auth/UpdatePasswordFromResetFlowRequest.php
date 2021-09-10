<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\FormRequest;

/**
 * Class UpdatePasswordFromResetFlowRequest
 * @package App\Http\Requests\Auth
 * @property mixed password
 */
final class UpdatePasswordFromResetFlowRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'password' => 'required',
            'password_reset_token' => 'required'
        ];
    }

}
