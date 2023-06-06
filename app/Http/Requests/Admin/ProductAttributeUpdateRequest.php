<?php

namespace App\Http\Requests\Admin;

use App\Models\ProductAttribute;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @mixin ProductAttribute
 */
class ProductAttributeUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['json'],
        ];
    }
}
