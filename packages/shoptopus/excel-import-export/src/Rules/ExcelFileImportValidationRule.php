<?php

namespace Shoptopus\ExcelImportExport\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\UploadedFile;

class ExcelFileImportValidationRule implements Rule
{
    private UploadedFile $file;

    public function __construct(UploadedFile $file)
    {
        $this->file = $file;
    }

    /**
     * @param $attribute
     * @param $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $extension = strtolower($this->file->getClientOriginalExtension());

        return in_array($extension, ['xls', 'xlsx']);
    }

    /**
     * @return string
     */
    public function message(): string
    {
        return 'The excel file must be a file of type: csv, xls, xlsx.';
    }

}
