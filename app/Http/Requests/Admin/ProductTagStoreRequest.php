<?php

namespace App\Http\Requests\Admin;

use App\Models\ProductTag;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @mixin ProductTag
 */
class ProductTagStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'json'],
            'description' => ['required', 'json'],
            'badge' => ['sometimes'],
            'display_badge' => ['sometimes'],
        ];
    }
}
