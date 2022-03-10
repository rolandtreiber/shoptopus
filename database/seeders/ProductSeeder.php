<?php

namespace Database\Seeders;

use App\Models\DiscountRule;
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
        Product::factory()->count(55)->hasFilecontents(rand(1, 3))->create();

        foreach (Product::all() as $product) {
            $product->productCategories()->attach((new ProductCategory)->findNthId(rand(1, ProductCategory::count()-1)));
        }

        $taggedCount = Product::count()-1;

        $used = [];
        for ($i = 0; $i < $taggedCount; $i++) {
            do {
                $productId = random_int(1, Product::count()-1);
            } while (in_array($productId, $used));
            $used[] = $productId;

            $tagsCount = random_int(1, ProductTag::count());
            $usedTags = [];
            for ($n = 1;$n < $tagsCount; $n++) {
                do {
                    $tagId = (new ProductTag)->findNthId(rand(1, ProductTag::count()-1));
                } while (in_array($tagId, $usedTags));
                $usedTags[] = $tagId;
                (new Product())->findNth($productId)->tags()->attach($tagId);
            }
        }

        $discounted = [];
        $discountedCount = random_int(1, round(Product::count() / 2));
        for ($i = 1; $i < $discountedCount; $i++) {
            do {
                $productId = rand(1, Product::count());
            } while (in_array($productId, $discounted));
            $discounted[] = $productId;
            (new Product())->findNth($productId)->discountRules()->attach((new DiscountRule)->findNthId(random_int(0, DiscountRule::count()-1)));
        }

        Product::all()->map(function (Product $product) {
            $images = $product->fileContents->pluck('id')->toArray();
            $selectedImageId = $images[random_int(0, count($images)-1)];
            $product->cover_photo_id = $selectedImageId;
            $product->save();
        });
    }
}
