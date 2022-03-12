<?php

namespace App\Http\Requests\Admin\BulkOperation;

class FileBulkOperationRequest extends BaseBulkOperationRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'ids' => ['required', 'array'],
            'ids.*' => ['exists:file_contents,id']
        ];
    }
}
