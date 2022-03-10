<?php

namespace App\Http\Requests\Address;

use Illuminate\Support\Facades\DB;
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
        return DB::table('addresses')
            ->where('id', $this->id)
            ->where('user_id', $this->user()->id)
            ->exists();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'user_id'  => 'sometimes|nullable|exists:users,id',
            'address_line_1' => "sometimes|required|string|min:2|max:255",
            'town' => "sometimes|required|string|min:2|max:255",
            'post_code' => "sometimes|required|string|min:2|max:255",
            'country' => "sometimes|required|string|min:2|max:255",
            'address_line_2' => "sometimes|nullable|string|min:2|max:255",
            'lat' => ['sometimes','nullable','regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'lon' => ['sometimes','nullable','regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/']
        ];
    }
}
