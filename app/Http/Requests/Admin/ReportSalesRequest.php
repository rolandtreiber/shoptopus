<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ReportSalesRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'revenue_over_time_range' => 'required',
            'products_breakdown_time_range' => 'required',
        ];
    }
}
