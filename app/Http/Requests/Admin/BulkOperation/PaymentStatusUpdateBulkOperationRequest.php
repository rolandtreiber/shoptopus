<?php

namespace App\Http\Requests\Admin\BulkOperation;

class PaymentStatusUpdateBulkOperationRequest extends BaseBulkOperationRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'ids' => ['required', 'array'],
            'ids.*' => ['exists:payments,id'],
        ];
    }
}
