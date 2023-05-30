<?php

namespace Database\Seeders\TestData;

use App\Models\ProductAttribute;
use App\Models\ProductAttributeOption;
use Illuminate\Database\Seeder;

class ProductAttributeOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $used = [];
        for ($i = 1; $i < 6; $i++) {
            do {
                $attributeId = (new ProductAttribute())->findNthId(rand(0, ProductAttribute::count()));
            } while (in_array($attributeId, $used));
            $used[] = $attributeId;
            $optionCount = rand(2, 10);
            ProductAttributeOption::factory()
                ->state([
                    'product_attribute_id' => $attributeId,
                ])
                ->count($optionCount)->create();
        }
    }
}
