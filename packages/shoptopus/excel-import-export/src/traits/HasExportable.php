<?php

namespace Shoptopus\ExcelImportExport\traits;

trait HasExportable
{
    /**
     * @return array
     */
    public function getExportableFields(): array
    {
        return $this->exportableFields ?: [];
    }

    /**
     * @param $exportableFields
     * @return void
     */
    public function setExportableFields($exportableFields): void
    {
        $this->exportableFields = $exportableFields;
    }

    /**
     * @return array
     */
    public function getExportableRelationships(): array
    {
        return $this->exportableRelationships ?: [];
    }

    /**
     * @param $exportableFields
     * @return void
     */
    public function setExportableRelationships($exportableRelationships): void
    {
        $this->exportableRelationships = $exportableRelationships;
    }
}
