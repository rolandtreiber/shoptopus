<?php

namespace Shoptopus\ExcelImportExport\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportTemplateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'model' => ['required'],
        ];
    }
}
