<?php

namespace Shoptopus\ExcelImportExport;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ModelTemplateExport implements WithTitle, WithHeadings, FromArray
{

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
        foreach ($this->modelData['importable'] as $key => $value) {
            if (in_array($key, $this->modelData['fillable'])) {
                $fieldData = ['name' => $key];
                $fieldData['is_field'] = true;
                if (in_array($key, $this->modelData['translatable'])) {
                    $fieldData['is_translatable'] = true;
                    $text = '';
                    foreach ($languages as $language) {
                        $text .= $language . ': ' . 'value' . '; ';
                    }
                    $fieldData['description'] = $text;
                } else {
                    $fieldData['is_translatable'] = false;
                    if (array_key_exists('description', $value)) {
                        $fieldData['description'] = $value['description'];
                    } else {
                        $fieldData['description'] = 'value';
                    }
                }
                $result[] = $fieldData;
            }
        }
        foreach ($this->modelData['relationships'] as $name => $relationship) {
            if (in_array($relationship['type'], self::ACCEPTED_RELATIONSHIP_TYPES)) {
                $fieldData = ['name' => $name];
                $fieldData['is_relationship'] = true;
                switch ($relationship['type']) {
                    case 'HasMany':
                    case 'BelongsToMany':
                        $fieldData['description'] = "Semicolon separated slugs of existing " . $name . ' (example: slug-1; slug-2)';
                        break;
                    case 'BelongsTo':
                    case 'HasOne':
                        $fieldData['description'] = "Slug of " . $name . ' (example: slug-1)';
                }

                $result[] = $fieldData;
            }
        }
        return $result;
    }

    public function headings(): array
    {
        $fields = $this->getFields();
        return array_map(function ($field) {
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
