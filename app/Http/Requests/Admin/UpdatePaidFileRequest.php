<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

/**
 * @property UploadedFile $file
 * @property string $title
 * @property string $description
 * @property string|null $original_file_name
 */
class UpdatePaidFileRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'file' => ['sometimes'],
            'title' => ['sometimes', 'json'],
            'description' => ['sometimes', 'json'],
        ];
    }
}
