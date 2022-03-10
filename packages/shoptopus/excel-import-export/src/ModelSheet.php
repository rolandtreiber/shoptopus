<?php

namespace Shoptopus\ExcelImportExport;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class ModelSheet implements WithTitle, FromCollection, WithHeadings, WithMapping {

    private string $modelName;
    private string $modelClass;
    private array $fields;
    private array $exportableRelationships;
    private array $relationships;
    private array $translatableFields;
    private array $languages;

    public function __construct(string $modelClass, string $modelName, array $exportable, array $translatableFields, array $relationships)
    {
        $this->modelName = $modelName;
        $this->modelClass = $modelClass;
        $this->fields = $exportable['fields'];
        $this->exportableRelationships = $exportable['relationships'];
        $this->relationships = $relationships;
        $this->translatableFields = $translatableFields;
        $this->languages = config('excel_import_export.languages');
    }

    public function collection()
    {
        $collection = (new $this->modelClass)->all();

        $result = $collection->map(function($item) {
            foreach ($this->translatableFields as $translatableField) {
                $translations = $item->getTranslations($translatableField);
                $text = '';
                foreach ($this->languages as $language) {
                    $text .= $language . ': '. $translations[$language] . '; ';
                }
                $item->$translatableField = $text;
            }
            return $item;
        });
        return $result;
    }

    public function title(): string
    {
        return $this->modelName;
    }

    public function headings(): array
    {
        return [...$this->fields, ...$this->exportableRelationships];
    }

    private function getRelationshipColumnValue($row, $relationshipName, $data) {

        switch ($data['type']) {
            case 'BelongsToMany':
            case 'HasMany':
                return implode(', ', $row->$relationshipName->pluck('slug')->toArray());
            case 'HasOne':
            case 'BelongsTo':
                return $row->$relationshipName ? $row->$relationshipName->slug : '';
            default:
                return $data['type'] . ' - ' . $data['model'];
        }

    }

    /**
     * @param $row
     * @return array
     */
    public function map($row): array
    {
        $result = $row->only($this->fields);

        foreach ($this->exportableRelationships as $exportableRelationship) {
            if (array_key_exists($exportableRelationship, $this->relationships)) {
                $result[$exportableRelationship] = $this->getRelationshipColumnValue($row, $exportableRelationship, $this->relationships[$exportableRelationship]);
            }
        }
        return $result;
    }
}
