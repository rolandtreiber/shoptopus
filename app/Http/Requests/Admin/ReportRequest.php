<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ReportRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'date_from' => 'required',
            'date_to' => 'required',
            'interval' => 'required',
            'models' => 'required',
            'type' => 'required',
            'randomize_colors' => 'required',
        ];
    }
}
