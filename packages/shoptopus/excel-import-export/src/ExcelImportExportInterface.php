<?php

namespace Shoptopus\ExcelImportExport;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

interface ExcelImportExportInterface
{
    public function import(UploadedFile $file, ExcelImportExportInterface $excelImportExport);

    public function validate(UploadedFile $file, ExcelImportExportInterface $excelImportExport);

    public function export(array $config = []);

    public function template(array $config = []);

    public function getClassName($modelName): string;

    public function getModelClasses($modelNames): array;

    public function getRelationships($class): array;

    public function getTranslatableFields($class);

    public function getFillableFields($class);

    public function getExportableFields($class);

    public function getImportableFields($class);

    public function getExportableRelationships($class);

    public function getImportableRelationships($class);

    public function getImportModelDetails(string $model): array;

    public function getExportModelMap(array $models = []): array;

    public function processUploadedRow(Collection $row);

    public function clearImportValidatorData();

    public function setImportModelDetails(array $data);

    public function getImportValidatorData();

    public function addImportRow($row, $valid);

    public function importRows(array $rows);
}
