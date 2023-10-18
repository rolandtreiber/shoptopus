<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use App\Services\Remote\Translations\TranslationService;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use stdClass;

class TestStore1Seeder extends Seeder
{
    private TranslationService $translationService;
    public function __construct(
        TranslationService $translationService
    ) {
        $this->translationService = $translationService;
    }
    private function importRecords(string $model, array $data): bool
    {
        $availableLanguages = array_keys(array_diff_key(config('app.locales_supported'), array_flip(["en"])));
        foreach ($data as $row) {
            if (array_key_exists('children', $row)) {
                $sanitised = array_diff_key($row, array_flip(["children"]));
            } else {
                $sanitised = $row;
            }
            $sanitised['id'] = (string)Str::orderedUuid();
            $sanitised['created_at'] = Carbon::now();
            array_walk($sanitised, function(&$a, $b) use ($availableLanguages) {
                if (str_contains($a, "(T)")) {
                    $translatable = str_replace("(T)", "", $a);
                    $translatables = $this->translationService->translate($translatable, $availableLanguages);
//                    $translatables = [];
                    $translatables['en'] = $translatable;
                    $a = $translatables;
                }
            });
            $record = (new $model());
            $newRecord = $record->fill($sanitised);
            $newRecord->save();
            if (array_key_exists('children', $row)) {
                $children = array_map(function ($item) use ($newRecord) {
                    $item['parent_id'] = $newRecord->id;
                    return $item;
                }, $row['children']);
                $this->importRecords($model, $children);
            }
        }
        return true;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoriesData = file_get_contents(__DIR__ . "/test-data/test-store-1/product-categories.json");
        // Import categories
        $this->importRecords(ProductCategory::class, json_decode($categoriesData, true));

    }
}
