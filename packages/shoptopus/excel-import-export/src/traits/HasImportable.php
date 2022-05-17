<?php

namespace Shoptopus\ExcelImportExport\traits;

trait HasImportable
{
    /**
     * @return array
     */
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

    /**
     * @param $importableFields
     * @return void
     */
    public function setImportableFields($importableFields): void
    {
        $this->importableFields = $importableFields;
    }

    /**
     * @return array
     */
    public function getImportableRelationships(): array
    {
        return $this->importableRelationships ?: [];
    }

    /**
     * @param $importableFields
     * @return void
     */
    public function setImportableRelationships($importableRelationships): void
    {
        $this->importableRelationships = $importableRelationships;
    }

}
