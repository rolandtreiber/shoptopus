<?php

namespace App\Http\Requests\DeliveryType;

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
            'name.*' => 'sometimes|required|string',
            'description.*' => 'sometimes|required|string|min:3',
            'price' => "sometimes|required|regex:/[\d]{1,2}.[\d]{1,2}/",
            'enabled' => 'sometimes|required|boolean',
            'enabled_by_default_on_creation' => 'sometimes|required|boolean'
        ];
    }
}
