<?php

namespace Shoptopus\ExcelImportExport;

use Closure;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ModelExportSheet implements WithTitle, FromCollection, WithHeadings, WithMapping, WithEvents
{
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

    /**
     * @return string
     */
    public function getModelName(): string
    {
        return $this->modelName;
    }

    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return $this->modelClass;
    }

    /**
     * @return array|mixed
     */
    public function getFields(): mixed
    {
        return $this->fields;
    }

    /**
     * @return array|mixed
     */
    public function getExportableRelationships(): mixed
    {
        return $this->exportableRelationships;
    }

    public function collection()
    {
        $collection = (new $this->modelClass)->all();

        $result = $collection->map(function ($item) {
            foreach ($this->translatableFields as $translatableField) {
                $translations = $item->getTranslations($translatableField);
                $text = '';
                foreach ($this->languages as $language) {
                    if (array_key_exists($language, $translations)) {
                        $text .= $language.': '.$translations[$language].'; ';
                    }
                }
                $item->$translatableField = $text;
            }

            return $item;
        });

        return $result;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return $this->modelName;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [...$this->fields, ...$this->exportableRelationships];
    }

    /**
     * @param $row
     * @param $relationshipName
     * @param $data
     * @return string
     */
    private function getRelationshipColumnValue($row, $relationshipName, $data): string
    {
        switch ($data['type']) {
            case 'BelongsToMany':
            case 'HasMany':
                return implode(', ', $row->$relationshipName->pluck('slug')->toArray());
            case 'HasOne':
            case 'BelongsTo':
                if ($row->$relationshipName) {
                    return $row->$relationshipName->slug ?: '';
                }

                return '';
            default:
                return $data['type'].' - '.$data['model'];
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

    /**
     * @return Closure[]
     */
    public function registerEvents(): array
    {
        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
        $headings = $this->headings();
        $slugColumnNumber = array_search('slug', $headings);
        $slugColumnId = null;
        if ($headings[$slugColumnNumber] === 'slug') {
            $slugColumnId = $columns[$slugColumnNumber];
        }

        return [
            AfterSheet::class => function (AfterSheet $event) use ($columns, $slugColumnId) {
                $event->sheet->getDelegate()->getStyle('A1:'.$columns[count($this->headings()) - 1].'1')
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('DD4B39');

                if ($slugColumnId !== null) {
                    $event->sheet->getDelegate()->getStyle($slugColumnId.'2:'.$slugColumnId.(count($this->collection()) + 1))
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('EBEBEB');
                }
            },
        ];
    }
}
