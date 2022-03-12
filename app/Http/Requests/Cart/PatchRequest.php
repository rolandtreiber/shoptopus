<?php

namespace App\Http\Requests\Cart;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class PatchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() : bool
    {
        return DB::table('carts')
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
            'ip_address' => "sometimes|nullable|string|max:100"
        ];
    }
}
