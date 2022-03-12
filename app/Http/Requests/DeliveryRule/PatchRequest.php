<?php

namespace App\Http\Requests\DeliveryRule;

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
            'delivery_type_id' => 'sometimes|required|exists:delivery_types,id',
            'postcodes' => 'sometimes|nullable|array',
            'min_weight' => 'sometimes|nullable|integer',
            'max_weight' => 'sometimes|nullable|integer',
            'min_distance' => 'sometimes|nullable|numeric',
            'max_distance' => 'sometimes|nullable|numeric',
            'distance_unit' => 'sometimes|required|string|in:mile,km',
            'lat' => ['sometimes', 'nullable','regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'lon' => ['sometimes', 'nullable','regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'enabled' => 'sometimes|required|boolean'
        ];
    }
}
