<?php

namespace App\Http\Requests\Local\Address;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class GetOrderForUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return DB::table('orders')
            ->where('id', $this->route('id'))
            ->where('user_id', $this->user()->id)
            ->exists();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'id' => 'required|string|exists:orders',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'id' => $this->route('id'),
        ]);
    }
}
