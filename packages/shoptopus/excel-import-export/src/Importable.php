<?php

namespace Shoptopus\ExcelImportExport;

interface Importable {

    public function getImportableFields(): array;
    public function setImportableFields($importable): void;
    public function getImportableRelationships(): array;
    public function setImportableRelationships($exportableRelationships): void;

}
