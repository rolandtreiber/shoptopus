<?php

namespace Shoptopus\ExcelImportExport;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Events\BeforeSheet;

class ModelImportSheet implements ToCollection, WithHeadingRow, WithEvents
{
    public $modelName;

    public $sheetData;

    protected ExcelImportExportInterface $excelImportExport;

    public function __construct(ExcelImportExportInterface $excelImportExport)
    {
        $this->excelImportExport = $excelImportExport;
        $this->sheetNames = [];
        $this->sheetData = [];
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $this->modelName = $event->getSheet()->getTitle();
            },
        ];
    }

    public function collection(Collection $collection)
    {
        $this->excelImportExport->setImportModelDetails($this->excelImportExport->getImportModelDetails(
            $this->excelImportExport->getClassName($this->modelName)
        ));
        $this->sheetData[] = $collection;
        foreach ($collection as $row) {
            $this->excelImportExport->processUploadedRow($row);
        }
    }
}
