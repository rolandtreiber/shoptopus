<?php

namespace Shoptopus\ExcelImportExport;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;

class ModelSheet implements WithTitle, FromCollection {

    private string $modelName;

    public function __construct(string $modelName)
    {
        $this->modelName = $modelName;
    }

    public function collection()
    {
        return (new $this->modelName)->all();
    }

    public function title(): string
    {
        return $this->modelName;
    }
}
