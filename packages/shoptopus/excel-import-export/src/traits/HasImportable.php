<?php

namespace Shoptopus\ExcelImportExport\traits;

trait HasImportable
{
    public function getImportableFields(): array
    {
        $importableFields = [];
        if ($this->importableFields) {
            foreach ($this->importableFields as $key => $value) {
                if (is_array($this->importableFields[$key])) {
                    $importableFields[$key] = $this->importableFields[$key];
                } else {
                    $importableFields[$value] = [];
                }
            }

            return $importableFields;
        }

        return [];
    }

    public function setImportableFields($importableFields): void
    {
        $this->importableFields = $importableFields;
    }

    public function getImportableRelationships(): array
    {
        return $this->importableRelationships ?: [];
    }

    /**
     * @param $importableFields
     */
    public function setImportableRelationships($importableRelationships): void
    {
        $this->importableRelationships = $importableRelationships;
    }
}
