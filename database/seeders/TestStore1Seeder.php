<?php

namespace Database\Seeders;

use App\Models\DeliveryRule;
use App\Models\DeliveryType;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeOption;
use App\Models\ProductCategory;
use App\Models\ProductTag;
use App\Models\VoucherCode;
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
            array_walk($sanitised, function(&$a, $b) use ($availableLanguages, $row) {
                $value = $a;
                if (str_contains($a, "(T)")) {
                    $translatable = str_replace("(T)", "", $a);
//                    $translatables = $this->translationService->translate($translatable, $availableLanguages);
                    $translatables = [];
                    $translatables['en'] = $translatable;
                    $a = $translatables;
                }

                if (str_contains($b, "_id") && $b !== "parent_id") {
                    $model = "App\\Models\\".str_replace("Id", "",  str_replace(" ", "", ucwords(str_replace("_", " ", $b))));
                    $a = ($model::where('slug', $a)->first())->id;
                }

                try {
                    if (str_contains($value, "(JSON)")) {
                        $a = json_decode(str_replace("(JSON)", "", $value));
                    }
                } catch (\TypeError $e) {
                    dd($a);
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
        // Import product categories
        $data = file_get_contents(__DIR__ . "/test-data/test-store-1/product-categories.json");
        $this->importRecords(ProductCategory::class, json_decode($data, true));

        // Import product attributes
        $data = file_get_contents(__DIR__ . "/test-data/test-store-1/product-attributes.json");
        $this->importRecords(ProductAttribute::class, json_decode($data, true));

        // Import product attribute options
        $data = file_get_contents(__DIR__ . "/test-data/test-store-1/product-attribute-options.json");
        $this->importRecords(ProductAttributeOption::class, json_decode($data, true));

        // Import product tags
        $data = file_get_contents(__DIR__ . "/test-data/test-store-1/product-tags.json");
        $this->importRecords(ProductTag::class, json_decode($data, true));

        // Import voucher codes
        $data = file_get_contents(__DIR__ . "/test-data/test-store-1/voucher-codes.json");
        $this->importRecords(VoucherCode::class, json_decode($data, true));

        // Import delivery types
        $data = file_get_contents(__DIR__ . "/test-data/test-store-1/delivery-types.json");
        $this->importRecords(DeliveryType::class, json_decode($data, true));

        // Import delivery rules
        $data = file_get_contents(__DIR__ . "/test-data/test-store-1/delivery-rules.json");
        $this->importRecords(DeliveryRule::class, json_decode($data, true));

    }
}
