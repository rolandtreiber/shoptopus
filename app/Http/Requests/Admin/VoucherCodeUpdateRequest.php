<?php

namespace App\Http\Requests\Admin;

use App\Enums\DiscountType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VoucherCodeUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'numeric', Rule::in(DiscountType::getValues())],
            'amount' => ['numeric'],
            'valid_from' => ['date'],
            'valid_until' => ['date'],
        ];
    }
}
