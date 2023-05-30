<?php

namespace Database\Seeders\TestData;

use App\Models\ProductAttribute;
use Illuminate\Database\Seeder;

class ProductAttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProductAttribute::factory()->count(5)->create();
    }
}
