<?php

namespace App\Http\Requests\Admin;

use App\Models\Banner;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @mixin Banner
 */
class BannerUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => ['json'],
            'description' => ['json'],
            'button_text' => ['json'],
            'button_url' => ['sometimes'],
        ];
    }
}
