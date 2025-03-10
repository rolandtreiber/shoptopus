<?php

namespace Shoptopus\ExcelImportExport\Http\Controllers;

use Shoptopus\ExcelImportExport\ExcelImportExportInterface;
use Shoptopus\ExcelImportExport\Http\Requests\ExportRequest;
use Shoptopus\ExcelImportExport\Http\Requests\ImportRequest;
use Shoptopus\ExcelImportExport\Http\Requests\ImportTemplateRequest;

class ImportExportController extends BaseController
{
    protected ExcelImportExportInterface $excelImportExport;

    public function __construct(ExcelImportExportInterface $excelImportExport)
    {
        $this->excelImportExport = $excelImportExport;
    }

    /**
     * @return bool
     */
    public function export(ExportRequest $request)
    {
        return $this->excelImportExport->export($request->toArray());
    }

    public function template(ImportTemplateRequest $request)
    {
        return $this->excelImportExport->template($request->toArray());
    }

    public function validateImport(ImportRequest $request)
    {
        return $this->excelImportExport->validate($request->file('file'), $this->excelImportExport);
    }

    public function import(ImportRequest $request)
    {
        return $this->excelImportExport->import($request->file('file'), $this->excelImportExport);
    }
}
