<?php

namespace Database\Seeders\TestData;

use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use Exception;
use Illuminate\Database\Seeder;

class CartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     *
     * @throws Exception
     */
    public function run(): void
    {
        $cartsCount = Cart::count();
        $products = Product::count();

        $cartsToPopulate = random_int(1, $cartsCount);

        $used = [];
        for ($i = 0; $i < $cartsToPopulate; $i++) {
            do {
                $selectedCartId = (new Cart)->findNthId(rand(1, $cartsCount));
            } while (in_array($selectedCartId, $used));
            $used[] = $selectedCartId;
            $selectedCart = Cart::find($selectedCartId);

            $usedProducts = [];
            $productTypesToAddCount = random_int(1, $products);
            for ($n = 0; $n < $productTypesToAddCount; $n++) {
                do {
                    $selectedProductId = (new Product)->findNthId(random_int(1, $products));
                } while (in_array($selectedProductId, $usedProducts));
                $usedProducts[] = $selectedProductId;
                $productVariantId = null;
                $variantsCount = ProductVariant::where('product_id', $selectedProductId)->count();
                if ($variantsCount > 0) {
                    $variants = ProductVariant::where('product_id', $selectedProductId)->get();
                    $productVariantId = ($variants[random_int(1, $variantsCount - 1)])->id;
                }
                $selectedCart->products()->attach($selectedProductId,
                    [
                        'quantity' => random_int(1, 10),
                        'product_variant_id' => $productVariantId,
                    ]
                );
            }
        }
    }
}
