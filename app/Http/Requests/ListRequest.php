<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'locale' => 'sometimes',
            'search' => 'sometimes',
            'page' => 'required',
            'paginate' => 'required'
        ];
    }
}
