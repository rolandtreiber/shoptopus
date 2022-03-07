<?php

namespace App\Http\Requests\Admin\BulkOperation;

use Illuminate\Foundation\Http\FormRequest;

class VoucherCodeBulkOperationRequest extends FormRequest
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
            'ids.*' => ['exists:voucher_codes,id']
        ];
    }
}
