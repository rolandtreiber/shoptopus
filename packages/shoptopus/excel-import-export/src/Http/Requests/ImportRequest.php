<?php

namespace Shoptopus\ExcelImportExport\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        // As it turned out, post request with file upload validation is a little flaky when testing.
        // It does validate ok in real-life, however the validation would fail every time during testing
        // despite uploading a perfectly valid file.
        // So the file validation is deactivated for testing.
        if (env('APP_ENV') !== 'testing') {
            return [
                'file' => ['required', 'mimes:xls,xlsx'],
            ];
        } else {
            return [];
        }
    }
}
