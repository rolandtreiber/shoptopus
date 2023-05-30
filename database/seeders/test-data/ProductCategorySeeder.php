<?php

namespace Database\Seeders\TestData;

use App\Models\DiscountRule;
use App\Models\ProductCategory;
use Exception;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     *
     * @throws Exception
     */
    public function run(): void
    {
        ProductCategory::factory()->count(10)->create();

        $subCategoriesCount = rand(5, 30);
        for ($i = 0; $i < $subCategoriesCount + 1; $i++) {
            $parentId = (new ProductCategory)->findNthId(rand(1, ProductCategory::count() - 1));
            ProductCategory::factory()->state(['parent_id' => $parentId])->create();
        }

        (new ProductCategory)->findNth(random_int(1, ProductCategory::count()))->discount_rules()->attach((new DiscountRule)->findNthId(random_int(1, DiscountRule::count() - 1)));
    }
}
