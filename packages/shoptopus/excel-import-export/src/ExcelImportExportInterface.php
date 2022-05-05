<?php

namespace Shoptopus\ExcelImportExport;

use Illuminate\Http\UploadedFile;

interface ExcelImportExportInterface {

    public function import(UploadedFile $file);
    public function export(array $config = []);
    public function template(array $config = []);

}
