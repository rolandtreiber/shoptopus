<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
            $attributesToImplementCount = rand(1, ProductAttribute::count());
            $usedAttributes = [];
            for ($n = 0; $n < $attributesToImplementCount; $n++) {
                do {
                    $attributeId = (new ProductAttribute)->findNthId(rand(1, ProductAttribute::count()));
                } while (in_array($attributeId, $usedAttributes));
                $usedAttributes[] = $attributeId;
                $attribute = ProductAttribute::find($attributeId);
                $options = $attribute->options;
                do {
                    $optionId = random_int(1, sizeof($options));
                } while (DB::table('product_attribute_product_variant')
                    ->where('product_variant_id', $productVariant->id)
                    ->where('product_attribute_id', $attribute->id)
                    ->where('product_attribute_option_id', $optionId)
                    ->first());
                $productVariant->attributes()->attach($attribute->id, ['product_attribute_option_id' => $options[$optionId-1]->id]);
            }
        }

        $used = [];
        for ($i = 0; $i < 3; $i++) {
            do {
                $productId = (new Product)->findNthId(rand(1, Product::count()));
            } while (in_array($productId, $used));
            $used[] = $productId;
            $attribute = ProductAttribute::find((new ProductAttribute)->findNthId(rand(1, ProductAttribute::count())));
            $options = $attribute->options;
            Product::find($productId)->attributes()->attach($attribute->id, ['product_attribute_option_id' => $options[random_int(1, count($options))-1]->id]);
        }

    }
}
