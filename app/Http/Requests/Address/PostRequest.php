<?php

namespace App\Http\Requests\Address;

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
            'address_line_1' => "required|string|min:2|max:255",
            'town' => "required|string|min:2|max:255",
            'post_code' => "required|string|min:2|max:255",
            'country' => "required|string|min:2|max:255",
            'address_line_2' => "nullable|string|min:2|max:255",
            'name' => "nullable|string|max:255",
            'lat' => ['nullable','regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'lon' => ['nullable','regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/']
        ];
    }
}
