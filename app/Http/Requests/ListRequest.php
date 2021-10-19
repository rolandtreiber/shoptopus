<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property mixed $paginate
 */
class ListRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'locale' => ['sometimes'],
            'search' => ['sometimes'],
            'filters' => ['sometimes', 'array'],
            'page' => ['required'],
            'paginate' => ['required'],
        ];
    }
}
