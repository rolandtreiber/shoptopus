<?php

namespace Shoptopus\ExcelImportExport;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ModelExport implements WithMultipleSheets
{
    private array $modelMap;

    public function __construct(array $modelMap)
    {
        $this->modelMap = $modelMap;
    }

    public function sheets(): array
    {
        $sheets = [];
        foreach ($this->modelMap as $modelClass => $data) {
            $sheets[] = new ModelExportSheet(
                $modelClass,
                $data['model'],
                $data['exportable'],
                $data['translatable'],
                $data['relationships'],
            );
        }

        return $sheets;
    }
}
