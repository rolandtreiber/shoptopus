<?php

namespace Database\Seeders;

use App\Models\ProductAttributeOption;
use Illuminate\Database\Seeder;

class ProductAttributeOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i < 6; $i++) {
            $optionCount = rand(2,10);
            ProductAttributeOption::factory()
                ->state([
                    'product_attribute_id' => $i
                ])
                ->count($optionCount)->create();

        }
    }
}
