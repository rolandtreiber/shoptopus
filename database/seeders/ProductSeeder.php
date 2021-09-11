<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductTag;
use Exception;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        Product::factory()->count(25)->create();

        foreach (Product::all() as $product) {
            $product->categories()->attach(rand(1, ProductCategory::count()));
        }

        $taggedCount = Product::count();

        $used = [];
        for ($i = 0; $i < $taggedCount; $i++) {
            do {
                $productId = rand(1, Product::count());
            } while (in_array($productId, $used));
            $used[] = $productId;

            $tagsCount = random_int(1, ProductTag::count());
            $usedTags = [];
            for ($n = 1;$n < $tagsCount; $n++) {
                do {
                    $tagId = rand(1, ProductTag::count());
                } while (in_array($tagId, $usedTags));
                $usedTags[] = $tagId;
                Product::find($productId)->tags()->attach($tagId);
            }
        }


    }
}
