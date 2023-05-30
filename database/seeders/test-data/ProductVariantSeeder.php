<?php

namespace Database\Seeders\TestData;

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
     *
     * @throws \Exception
     */
    public function run(): void
    {
        $variableProductCount = Product::count() / 2;

        $used = [];
        for ($i = 0; $i < $variableProductCount; $i++) {
            do {
                $productId = rand(1, Product::count());
            } while (in_array($productId, $used));
            $used[] = $productId;
            $uuid = (new Product())->findNth($productId)->id;
            ProductVariant::factory()->state([
                'product_id' => $uuid,
            ])->count(rand(5, 15))->hasFilecontents(rand(1, 3))->create();
        }

        $attributeIds = ProductAttribute::all()->pluck('id')->toArray();
        foreach (ProductVariant::all() as $productVariant) {
            $attributesToImplementCount = rand(1, count($attributeIds));
            $usedAttributes = [];
            for ($n = 0; $n < $attributesToImplementCount; $n++) {
                do {
                    $attribute = ProductAttribute::find($attributeIds[rand(0, count($attributeIds) - 1)]);
                } while (in_array($attribute->id, $usedAttributes));
                $usedAttributes[] = $attribute->id;
                $options = $attribute->options;
                if (count($options) > 0) {
                    do {
                        $optionId = random_int(0, count($options) - 1);
                    } while (DB::table('product_attribute_product_variant')
                        ->where('product_variant_id', $productVariant->id)
                        ->where('product_attribute_id', $attribute->id)
                        ->where('product_attribute_option_id', $optionId)
                        ->first());
                    $productVariant->product_variant_attributes()->attach($attribute->id, ['product_attribute_option_id' => $options[$optionId]->id]);
                }
            }
        }

        $used = [];
        for ($i = 0; $i < 3; $i++) {
            do {
                $productId = rand(0, Product::count() - 1);
            } while (in_array($productId, $used));
            $used[] = $productId;
            if (ProductAttribute::count() > 0) {
                $attribute = ProductAttribute::find($attributeIds[rand(0, count($attributeIds) - 1)]);
                $options = $attribute->options;
                count($options) > 0 && (new Product)->findNth($productId)->product_attributes()->attach($attribute->id, ['product_attribute_option_id' => $options[random_int(1, count($options)) - 1]->id]);
            }
        }
    }
}
