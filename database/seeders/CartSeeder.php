<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\Product;
use Exception;
use Illuminate\Database\Seeder;

class CartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run()
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

            $usedProductTypes = [];
            $productTypesToAddCount = random_int(1, $products);
            for ($n = 0; $n < $productTypesToAddCount; $n++) {
                do {
                    $selectedProductId = (new Product)->findNthId(random_int(1, $products));
                } while (in_array($selectedProductId, $usedProductTypes));
                $usedProductTypes[] = $selectedProductId;
                $selectedCart->products()->attach($selectedProductId, ['amount' => random_int(1, 10)]);
            }
        }
    }
}
