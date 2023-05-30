<?php

namespace App\Http\Requests\Admin\BulkOperation;

class DeliveryTypeBulkOperationRequest extends BaseBulkOperationRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'ids' => ['required', 'array'],
            'ids.*' => ['exists:delivery_types,id'],
        ];
    }
}
