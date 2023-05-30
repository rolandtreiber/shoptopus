<?php

namespace App\Http\Requests\Admin;

use App\Models\ProductTag;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @mixin ProductTag
 */
class ProductTagUpdateRequest extends FormRequest
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
