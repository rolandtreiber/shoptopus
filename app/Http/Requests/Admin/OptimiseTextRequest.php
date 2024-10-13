<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property string $text
 * @property array $target_languages
 */
class OptimiseTextRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'text' => ['required', 'min:3'],
            'target_languages.*' => ['sometimes', Rule::in(array_keys(config('app.locales_supported')))]
        ];
    }
}
