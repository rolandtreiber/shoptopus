<?php

namespace App\Http\Requests\VoucherCode;

use App\Enums\DiscountTypes;
use Illuminate\Foundation\Http\FormRequest;

class PatchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() : bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'amount' => "sometimes|required|regex:/[\d]{1,2}.[\d]{1,2}/",
            'valid_from' => "sometimes|required|date",
            'valid_until' => "sometimes|required|date",
            'type' => 'sometimes|required|in:' . implode(',', DiscountTypes::getValues()),
            'enabled' => 'sometimes|required|boolean'
        ];
    }
}
