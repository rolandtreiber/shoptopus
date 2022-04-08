<?php

namespace App\Http\Requests\Auth;

use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Http\FormRequest;

class HandleOAuthProviderCallbackRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() : bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'provider' => 'required|string|in:' .  implode(',', Config::get('social_login_providers.providers')),
            'code' => 'required|string'
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'provider' => $this->provider,
        ]);
    }
}
