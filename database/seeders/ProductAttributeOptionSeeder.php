<?php

namespace Database\Seeders;

use App\ProductAttributeOption;
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
        ProductAttributeOption::factory()->count(5)->create();
    }
}
