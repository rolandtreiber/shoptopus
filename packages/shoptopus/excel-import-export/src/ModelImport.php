<?php

namespace Shoptopus\ExcelImportExport;

use Maatwebsite\Excel\Concerns\ToArray;

class ModelImport implements ToArray
{

    public function array(array $array)
    {
        dd($array);
    }
}
