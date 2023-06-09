<?php

namespace App\Http\Requests\Admin;

use App\Enums\DiscountType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VoucherCodeStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'numeric', Rule::in(DiscountType::getValues())],
            'amount' => ['required', 'numeric'],
            'valid_from' => ['required', 'date'],
            'valid_until' => ['required', 'date'],
        ];
    }
}
