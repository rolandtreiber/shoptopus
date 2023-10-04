<?php

namespace Database\Seeders\TestData;

use App\Models\DiscountRule;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductTag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VirtualProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $virtualProducts = Product::factory()->state([
            'virtual' => true
        ])->count(10)->hasPaidFilecontents(rand(1, 3))->create();

        $productIds = $virtualProducts->pluck('id')->toArray();

        foreach ($virtualProducts as $product) {
            do {
                $categoryId = (new ProductCategory)->findNthId(rand(1, ProductCategory::count() - 1));
                $duplicate = DB::table('product_product_category')->where('product_id', $product->id)
                    ->where('product_category_id', $categoryId)->first();
            } while ($duplicate !== null);
            $product->product_categories()->attach($categoryId);
        }

        $taggedCount = $virtualProducts->count() / 2;
        $productTotalCount = $virtualProducts->count() - 1;

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
                Product::find($productIds[$productId])->product_tags()->attach((new ProductTag)->findNthId($tagId));
            }
        }

        $discounted = [];
        $discountedCount = random_int(1, round($virtualProducts->count() / 2));
        for ($i = 1; $i < $discountedCount; $i++) {
            do {
                $productId = rand(1, $virtualProducts->count());
            } while (in_array($productId, $discounted));
            $discounted[] = $productId;
            Product::find($productIds[$productId])->discount_rules()->attach((new DiscountRule)->findNthId(random_int(0, DiscountRule::count() - 1)));
        }

        $virtualProducts->map(function (Product $product) {
            $product->save();
        });
    }
}
