<?php

namespace Database\Seeders\TestData;

use App\Models\DiscountRule;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductTag;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     *
     * @throws Exception
     */
    public function run()
    {
        Product::factory()->count(55)->hasFilecontents(rand(1, 3))->create();

        foreach (Product::all() as $product) {
            do {
                $categoryId = (new ProductCategory)->findNthId(rand(1, ProductCategory::count() - 1));
                $duplicate = DB::table('product_product_category')->where('product_id', $product->id)
                ->where('product_category_id', $categoryId)->first();
            } while ($duplicate !== null);
            $product->product_categories()->attach($categoryId);
        }

        $taggedCount = Product::count() / 2;
        $productTotalCount = Product::count() - 1;

        $used = [];
        for ($i = 0; $i < $taggedCount; $i++) {
            do {
                $productId = random_int(1, $productTotalCount);
            } while (in_array($productId, $used));
            $used[] = $productId;

            $tagsCount = random_int(1, ProductTag::count() / 3);
            $productTagTotalCount = ProductTag::count() - 2;
            $usedTags = [];
            for ($n = 1; $n < $tagsCount; $n++) {
                do {
                    $tagId = rand(1, $productTagTotalCount);
                } while (in_array($tagId, $usedTags));
                $usedTags[] = $tagId;
                (new Product())->findNth($productId)->product_tags()->attach((new ProductTag)->findNthId($tagId));
            }
        }

        $discounted = [];
        $discountedCount = random_int(1, round(Product::count() / 2));
        for ($i = 1; $i < $discountedCount; $i++) {
            do {
                $productId = rand(1, Product::count());
            } while (in_array($productId, $discounted));
            $discounted[] = $productId;
            (new Product())->findNth($productId)->discount_rules()->attach((new DiscountRule)->findNthId(random_int(0, DiscountRule::count() - 1)));
        }

        Product::all()->map(function (Product $product) {
            $product->save();
        });
    }
}
