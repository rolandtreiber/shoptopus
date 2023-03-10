<?php

namespace App\Http\Requests;

use App\Exceptions\ApiValidationFailedException;
use Illuminate\Contracts\Validation\Validator;

class FormRequest extends \Illuminate\Foundation\Http\FormRequest
{
    /**
     * @throws ApiValidationFailedException
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new ApiValidationFailedException($validator->errors());
    }
}
