<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property mixed $menu_image
 * @property mixed $header_image
 * @property boolean $clear_images
 */
class ProductCategoryUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['json'],
            'clear_images' => ['sometimes', 'boolean']
        ];
    }
}
