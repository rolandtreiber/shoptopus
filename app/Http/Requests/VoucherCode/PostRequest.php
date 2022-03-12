<?php

namespace App\Http\Requests\VoucherCode;

use App\Enums\DiscountType;
use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
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
            'amount' => "required|regex:/[\d]{1,2}.[\d]{1,2}/",
            'valid_from' => "required|date",
            'valid_until' => "required|date",
            'type' => 'sometimes|required|in:' . implode(',', DiscountType::getValues()),
            'enabled' => 'sometimes|required|boolean'
        ];
    }
}
