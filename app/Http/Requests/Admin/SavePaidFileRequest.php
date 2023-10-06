<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

/**
 * @property UploadedFile $file
 * @property string $title
 * @property string $description
 * @property string $original_file_name
 */
class SavePaidFileRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'file' => ['required'],
            'original_file_name' => ['required'],
            'title' => ['required', 'json'],
            'description' => ['required', 'json']
        ];
    }
}
