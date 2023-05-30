<?php

namespace App\Http\Requests\Admin\BulkOperation;

class RatingBulkOperationRequest extends BaseBulkOperationRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'ids' => ['required', 'array'],
            'ids.*' => ['exists:ratings,id'],
        ];
    }
}
