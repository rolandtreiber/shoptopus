<?php

namespace Shoptopus\ExcelImportExport\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExportRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'models' => ['required', 'array'],
            'name' => ['required'],
        ];
    }
}
