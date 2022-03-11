<?php

namespace Shoptopus\ExcelImportExport;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ModelTemplateExport implements WithHeadings, FromArray {

    private array $modelMap;

    public function __construct(array $modelMap)
    {
        $this->modelMap = $modelMap;
    }

    public function headings(): array
    {
        return [
            'test'
        ];
    }

    public function array(): array
    {
        return [
            'test'
        ];
    }
}
