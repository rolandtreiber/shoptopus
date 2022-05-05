<?php

namespace Shoptopus\ExcelImportExport\Http\Controllers;

use Illuminate\Http\Request;
use Shoptopus\ExcelImportExport\ExcelImportExportInterface;
use Shoptopus\ExcelImportExport\Http\Requests\ExportRequest;
use Shoptopus\ExcelImportExport\Http\Requests\ImportTemplateRequest;
use Shoptopus\ExcelImportExport\Rules\ExcelFileImportValidationRule;

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
        return $this->excelImportExport->template($request->toArray());
    }

    public function import(Request $request)
    {
        if ($request->hasFile('file')) {
            $request->validate([
                'file' => ['required', new ExcelFileImportValidationRule($request->file('file'))],
            ]);
            return $this->excelImportExport->import($request->file('file'));
        } else {
            return 'no file attached';
        }
    }

}
