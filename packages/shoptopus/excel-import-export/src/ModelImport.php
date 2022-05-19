<?php

namespace Shoptopus\ExcelImportExport;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ModelImport implements WithMultipleSheets
{
    protected ExcelImportExportInterface $excelImportExport;

    public function __construct(ExcelImportExportInterface $excelImportExport)
    {
        $this->excelImportExport = $excelImportExport;
    }

    public function sheets(): array
    {
        return [
            new ModelImportSheet($this->excelImportExport)
        ];
    }
}
