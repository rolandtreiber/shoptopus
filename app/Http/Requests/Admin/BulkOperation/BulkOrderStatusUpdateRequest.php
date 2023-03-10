<?php

namespace App\Http\Requests\Admin\BulkOperation;

use App\Enums\OrderStatus;
use Illuminate\Validation\Rule;

class BulkOrderStatusUpdateRequest extends BaseBulkOperationRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'ids' => ['required', 'array'],
            'ids.*' => ['exists:orders,id'],
            'status' => [
                'required',
                Rule::in(OrderStatus::getValues()),
            ],
        ];
    }
}
