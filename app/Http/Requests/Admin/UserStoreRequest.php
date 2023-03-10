<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string|null $avatar
 * @property mixed $roles
 */
class UserStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required'],
            'last_name' => ['required'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'avatar' => ['sometimes'],
            'roles' => ['required', 'array'],
        ];
    }
}
