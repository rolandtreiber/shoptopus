<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $note
 * @property mixed $noteable_id
 * @property mixed $noteable_type
 */
class NoteStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $validNoteables = [
            'user', 'order', 'payment', 'voucher_code', 'discount_rule', 'delivery_type', 'product', 'product_variant', 'product_category', 'product_tag', 'product_attribute', 'product_attribute_option'
        ];

        return [
            'note' => ['required', 'max:500'],
            'noteable_type' => ['required', Rule::in($validNoteables)],
            'noteable_id' => ['required']
        ];
    }
}
