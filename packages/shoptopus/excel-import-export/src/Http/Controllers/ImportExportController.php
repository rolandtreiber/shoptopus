<?php

namespace Shoptopus\ExcelImportExport\Http\Controllers;

use Shoptopus\ExcelImportExport\ExcelImportExportInterface;
use Shoptopus\ExcelImportExport\Http\Requests\ExportRequest;
use Shoptopus\ExcelImportExport\Http\Requests\ImportTemplateRequest;

class ImportExportController extends BaseController {

    protected ExcelImportExportInterface $excelImportExport;

    public function __construct(ExcelImportExportInterface $excelImportExport)
    {
        $this->excelImportExport = $excelImportExport;
    }

    /**
     * @param ExportRequest $request
     * @return bool
     */
    public function export(ExportRequest $request)
    {
        return $this->excelImportExport->export($request->toArray());
    }

    public function template(ImportTemplateRequest $request)
    {
        return 'hello';
    }

}
