<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Product::factory()->count(25)->create();

        foreach (Product::all() as $product) {
            $product->categories()->attach(rand(1, ProductCategory::count()));
        }

    }
}
