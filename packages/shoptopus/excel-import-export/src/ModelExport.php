<?php

namespace Shoptopus\ExcelImportExport;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ModelExport implements FromCollection, WithMultipleSheets {

    private array $modelMap;

    public function __construct(array $modelMap)
    {
        $this->modelMap = $modelMap;
    }

    public function collection()
    {
        return Product::all();
    }

    public function sheets(): array
    {
        $sheets = [];
        foreach ($this->modelMap as $modelClass => $data) {
            $sheets[] = new ModelSheet($modelClass);
        }
        return $sheets;
    }
}
