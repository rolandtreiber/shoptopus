<?php

namespace Database\Seeders;

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
     * @throws Exception
     */
    public function run()
    {
        ProductCategory::factory()->count(10)->create();

        $subCategoriesCount = rand(5, 30);
        for ($i = 0; $i < $subCategoriesCount + 1; $i++) {
            $parentId = rand(1, ProductCategory::count()-1);
            ProductCategory::factory()->state(['parent_id' => $parentId])->create();
        }

        ProductCategory::find(random_int(1, ProductCategory::count()))->discountRules()->attach(DiscountRule::find(random_int(1, DiscountRule::count())));
    }
}
