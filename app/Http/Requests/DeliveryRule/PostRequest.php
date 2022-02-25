<?php

namespace App\Http\Requests\DeliveryRule;

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
            'delivery_type_id' => 'required|exists:delivery_types,id',
            'postcodes' => 'nullable|array',
            'min_weight' => 'nullable|integer',
            'max_weight' => 'nullable|integer',
            'min_distance' => 'nullable|numeric',
            'max_distance' => 'nullable|numeric',
            'distance_unit' => 'sometimes|required|string|in:mile,km',
            'lat' => ['nullable','regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'lon' => ['nullable','regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'enabled' => 'sometimes|required|boolean'
        ];
    }
}
