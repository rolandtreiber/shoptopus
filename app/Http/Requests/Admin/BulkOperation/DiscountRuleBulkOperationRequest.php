<?php

namespace App\Http\Requests\Admin\BulkOperation;

class DiscountRuleBulkOperationRequest extends BaseBulkOperationRequest
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
            'ids.*' => ['exists:discount_rules,id'],
        ];
    }
}
