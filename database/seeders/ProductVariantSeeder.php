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
     * @throws \Exception
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
            $uuid = (new Product())->findNth($productId)->id;
            ProductVariant::factory()->state([
                'product_id' => $uuid
            ])->count(rand(5, 15))->create();
        }

        foreach (ProductVariant::all() as $productVariant) {
            $attribute = ProductAttribute::find(rand(1, ProductAttribute::count()));
            $options = $attribute->options;
            $implementedOptionsCount = random_int(1, sizeof($options));
            $usedOptions = [];
            for ($i = 0;$i < $implementedOptionsCount;$i++) {
                do {
                    $optionId = random_int(1, sizeof($options));
                } while (in_array($optionId, $usedOptions));
                $usedOptions[] = $optionId;
                $selectedOptionId = $options[$optionId-1]->id;
                $productVariant->attributes()->attach($attribute->id, ['product_attribute_option_id' => $selectedOptionId]);
            }
        }

        for ($i = 0; $i < 3; $i++) {
            do {
                $productId = rand(1, Product::count());
            } while (in_array($productId, $used));
            $used[] = $productId;
            (new Product())->findNth($productId)->attributes()->attach(rand(1, ProductAttribute::count()));
        }

    }
}
