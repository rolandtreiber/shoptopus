<?php

namespace Shoptopus\ExcelImportExport;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ModelTemplateExport implements WithTitle, WithHeadings, FromArray {

    private array $modelData;

    const ACCEPTED_RELATIONSHIP_TYPES = [
        'HasMany',
        'BelongsToMany',
        'BelongsTo',
        'HasOne'
    ];

    public function __construct(array $modelData)
    {
        $this->modelData = $modelData;
    }

    private function getFields(): array
    {
        $languages = config('excel_import_export.languages');
        $result = [];
        foreach ($this->modelData['fillable'] as $field) {
            $fieldData = ['name' => $field];
            $fieldData['is_field'] = true;
            if (in_array($field, $this->modelData['translatable'])) {
                $fieldData['is_translatable'] = true;
                $text = '';
                foreach ($languages as $language) {
                    $text .= $language . ': '. 'value' . '; ';
                }
                $fieldData['description'] = $text;
            } else {
                $fieldData['is_translatable'] = false;
                $fieldData['description'] = 'value';
            }
            $result[] = $fieldData;
        }
        foreach ($this->modelData['relationships'] as $name => $relationship) {
            if (in_array($relationship['type'], self::ACCEPTED_RELATIONSHIP_TYPES)) {
                $fieldData = ['name' => $name];
                $fieldData['is_relationship'] = true;
                $fieldData['description'] = "Semicolon separated slugs of existing " . $name . ' (example: slug-1; slug-2)';
                $result[] = $fieldData;
            }
        }
        return $result;
    }

    public function headings(): array
    {
        $fields = $this->getFields();
        return array_map(function($field) {
            return $field['name'];
        }, $fields);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return $this->modelData['name'];
    }

    public function array(): array
    {
        $fields = $this->getFields();
        $data = [];
        foreach ($fields as $field) {
            $data[] = $field['description'];
        }
        return [$data];
    }
}
