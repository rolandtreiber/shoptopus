<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class VoucherCodeStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'numeric'],
            'amount' => ['required', 'numeric'],
            'valid_from' => ['required', 'date'],
            'valid_until' => ['required', 'date']
        ];
    }
}
