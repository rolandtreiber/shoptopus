<?php

namespace Shoptopus\ExcelImportExport;

interface Exportable
{
    public function getExportableFields(): array;

    public function setExportableFields($exportable): void;

    public function getExportableRelationships(): array;

    public function setExportableRelationships($exportableRelationships): void;
}
