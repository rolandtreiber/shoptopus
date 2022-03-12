<?php

namespace Shoptopus\ExcelImportExport;

interface ExcelImportExportInterface {

    public function import(array $config = []);
    public function export(array $config = []);

}
