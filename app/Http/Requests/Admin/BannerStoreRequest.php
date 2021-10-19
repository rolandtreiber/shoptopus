<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $background_image
 */
class BannerStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => ['required', 'json'],
            'description' => ['required', 'json'],
            'button_text' => ['sometimes', 'json'],
            'button_url' => ['sometimes'],
        ];
    }
}
