<?php

namespace App\Http\Requests\Admin;

use App\Rules\ContainsEmail;
use Illuminate\Foundation\Http\FormRequest;

class SendEmailRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'addresses' => ['required'],
            'addresses.*' => ['required', new ContainsEmail()],
        ];
    }
}
