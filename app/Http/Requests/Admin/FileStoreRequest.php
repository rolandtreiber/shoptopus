<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $model
 * @property string $id
 * @property mixed $file
 * @property mixed $files
 */
class FileStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'model' => ['required'],
            'id' => ['required'],
            'file' => ['sometimes'],
            'files' => ['sometimes'],
        ];
    }
}
