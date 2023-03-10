<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class VoucherCodeUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'type' => ['numeric'],
            'amount' => ['numeric'],
            'valid_from' => ['date'],
            'valid_until' => ['date'],
        ];
    }
}
