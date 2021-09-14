<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

class ProductVariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $variableProductCount = Product::count()-4;

        $used = [];
        for ($i = 0; $i < $variableProductCount; $i++) {
            do {
                $productId = rand(1, Product::count());
            } while (in_array($productId, $used));
            $used[] = $productId;
            ProductVariant::factory()->state([
                'product_id' => $productId
            ])->count(rand(5, 15))->create();
        }

        foreach (ProductVariant::all() as $productVariant) {
            $productVariant->attributes()->attach(rand(1, ProductAttribute::count()));
        }

        for ($i = 0; $i < 3; $i++) {
            do {
                $productId = rand(1, Product::count());
            } while (in_array($productId, $used));
            $used[] = $productId;
            Product::find($productId)->attributes()->attach(rand(1, ProductAttribute::count()));
        }

    }
}
