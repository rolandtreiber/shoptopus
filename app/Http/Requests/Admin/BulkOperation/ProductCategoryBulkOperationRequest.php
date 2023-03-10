<?php

namespace App\Http\Requests\Admin\BulkOperation;

class ProductCategoryBulkOperationRequest extends BaseBulkOperationRequest
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
            'ids.*' => ['exists:product_categories,id'],
        ];
    }
}
