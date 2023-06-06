<?php

namespace Shoptopus\ExcelImportExport\traits;

trait HasExportable
{
    public function getExportableFields(): array
    {
        return $this->exportableFields ?: [];
    }

    public function setExportableFields($exportableFields): void
    {
        $this->exportableFields = $exportableFields;
    }

    public function getExportableRelationships(): array
    {
        return $this->exportableRelationships ?: [];
    }

    /**
     * @param $exportableFields
     */
    public function setExportableRelationships($exportableRelationships): void
    {
        $this->exportableRelationships = $exportableRelationships;
    }
}
