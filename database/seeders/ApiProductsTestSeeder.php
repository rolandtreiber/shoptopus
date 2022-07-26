<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductTag;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use App\Models\ProductCategory;
use App\Models\ProductAttribute;
use App\Enums\ProductAttributeType;
use App\Models\ProductAttributeOption;

class ApiProductsTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->addRelationships(Product::factory()->count(1)->create([
            'price' => 24.99,
            'headline' => 'Sustainable Materials',
            'subtitle' => "Women's T-shirt",
            'name' => 'Sportswear Essential',
            'short_description' => 'Take flight in your new favourite tee. Rep an icon with bold graphics that remind you to reach for the sky, printed on heavyweight cotton for a premium look and feel.'
        ]));
    }

    protected function addRelationships($products)
    {
        foreach ($products as $index => $product) {
            // Product Tags
            ProductTag::factory()->count(2)->create()->each(fn($tag) => $tag->products()->attach($product->id));

            // Product Category
            $parent_category = ProductCategory::factory()->create(['name' => 'Women']);
            $product_category = ProductCategory::factory()->create([
                'name' => 'T-shirts',
                'parent_id' => $parent_category->id
            ]);
            $product->product_categories()->attach([$parent_category->id, $product_category->id]);

            // Color attributes
            $attribute_color = ProductAttribute::factory()->create([
                'type' => ProductAttributeType::Color,
                'name' => 'color'
            ]);
            $attribute_color_color_options = ProductAttributeOption::factory()
                ->count(3)
                ->create(['product_attribute_id' => $attribute_color->id]);

            $attribute_color_color_options[0]->update([
                'name' => 'red',
                'value' => 'rgb(255, 0, 0)'
            ]);
            $attribute_color_color_options[1]->update([
                'name' => 'blue',
                'value' => 'rgb(0, 0, 255)'
            ]);
            $attribute_color_color_options[2]->update([
                'name' => 'green',
                'value' => 'rgb(0, 255, 0)'
            ]);

            // Size attributes
            $attribute_size = ProductAttribute::factory()->create([
                'type' => ProductAttributeType::Text,
                'name' => 'size'
            ]);

            $attribute_size_options = ProductAttributeOption::factory()
                ->count(5)
                ->create(['product_attribute_id' => $attribute_size->id]);

            $attribute_size_options[0]->update([
                'name' => 'xs',
                'value' => 'xs'
            ]);
            $attribute_size_options[1]->update([
                'name' => 'sm',
                'value' => 'sm'
            ]);
            $attribute_size_options[2]->update([
                'name' => 'md',
                'value' => 'md'
            ]);
            $attribute_size_options[3]->update([
                'name' => 'lg',
                'value' => 'lg'
            ]);
            $attribute_size_options[4]->update([
                'name' => 'xl',
                'value' => 'xl'
            ]);

            // Product Variants
            $variant1 = ProductVariant::factory()->create([
                'product_id' => $product->id,
                'stock' => 3
            ]);

            $variant2 = ProductVariant::factory()->create([
                'product_id' => $product->id,
                'stock' => 6
            ]);

            $variant3 = ProductVariant::factory()->create([
                'product_id' => $product->id,
                'stock' => 2
            ]);

            $variant4 = ProductVariant::factory()->create([
                'product_id' => $product->id,
                'stock' => 12
            ]);

            // Add attributes to variant1
            $attribute_color_color_options->each(fn($option) =>
                $variant1->product_variant_attributes()->attach($attribute_color, ['product_attribute_option_id' => $option->id])
            );

            $attribute_size_options->each(fn($option) =>
                $variant1->product_variant_attributes()->attach($attribute_size, ['product_attribute_option_id' => $option->id])
            );

            // Add attributes to variant2
            $variant2->product_variant_attributes()->attach($attribute_color, ['product_attribute_option_id' => $attribute_color_color_options[1]->id]);
            $variant2->product_variant_attributes()->attach($attribute_color, ['product_attribute_option_id' => $attribute_color_color_options[2]->id]);

            $variant2->product_variant_attributes()->attach($attribute_size, ['product_attribute_option_id' => $attribute_size_options[1]->id]);
            $variant2->product_variant_attributes()->attach($attribute_size, ['product_attribute_option_id' => $attribute_size_options[3]->id]);
            $variant2->product_variant_attributes()->attach($attribute_size, ['product_attribute_option_id' => $attribute_size_options[4]->id]);

            // Add attributes to variant3
            $variant3->product_variant_attributes()->attach($attribute_color, ['product_attribute_option_id' => $attribute_color_color_options[1]->id]);

            $variant3->product_variant_attributes()->attach($attribute_size, ['product_attribute_option_id' => $attribute_size_options[0]->id]);
            $variant3->product_variant_attributes()->attach($attribute_size, ['product_attribute_option_id' => $attribute_size_options[2]->id]);
            $variant3->product_variant_attributes()->attach($attribute_size, ['product_attribute_option_id' => $attribute_size_options[3]->id]);
            $variant3->product_variant_attributes()->attach($attribute_size, ['product_attribute_option_id' => $attribute_size_options[4]->id]);

            // Add attributes to variant4
            $variant4->product_variant_attributes()->attach($attribute_color, ['product_attribute_option_id' => $attribute_color_color_options[0]->id]);
            $variant4->product_variant_attributes()->attach($attribute_color, ['product_attribute_option_id' => $attribute_color_color_options[1]->id]);

            $variant4->product_variant_attributes()->attach($attribute_size, ['product_attribute_option_id' => $attribute_size_options[0]->id]);
            $variant4->product_variant_attributes()->attach($attribute_size, ['product_attribute_option_id' => $attribute_size_options[2]->id]);
            $variant4->product_variant_attributes()->attach($attribute_size, ['product_attribute_option_id' => $attribute_size_options[3]->id]);


//            // Color
//            $attribute_color = ProductAttribute::factory()->create([
//                'type' => ProductAttributeType::Color,
//                'name' => 'color'
//            ]);
//
//            $attribute_color_options = ProductAttributeOption::factory()
//                ->count(3)
//                ->create(['product_attribute_id' => $attribute_color->id]);
//
//            $attribute_color_options[0]->update([
//                'name' => 'red',
//                'value' => 'rgb(255, 0, 0)'
//            ]);
//            $attribute_color_options[1]->update([
//                'name' => 'blue',
//                'value' => 'rgb(0, 0, 255)'
//            ]);
//            $attribute_color_options[2]->update([
//                'name' => 'green',
//                'value' => 'rgb(0, 255, 0)'
//            ]);
//
//            $attribute_color_options->each(fn($option) =>
//                $product->product_attributes()->attach($attribute_color->id, ['product_attribute_option_id' => $option->id])
//            );

//            // Size
//            $attribute_size = ProductAttribute::factory()->create([
//                'type' => ProductAttributeType::Text,
//                'name' => 'size'
//            ]);
//
//            $attribute_size_options = ProductAttributeOption::factory()
//                ->count(5)
//                ->create(['product_attribute_id' => $attribute_size->id]);
//
//            $attribute_size_options[0]->update([
//                'name' => 'xs',
//                'value' => 'xs'
//            ]);
//            $attribute_size_options[1]->update([
//                'name' => 'sm',
//                'value' => 'sm'
//            ]);
//            $attribute_size_options[2]->update([
//                'name' => 'md',
//                'value' => 'md'
//            ]);
//            $attribute_size_options[3]->update([
//                'name' => 'lg',
//                'value' => 'lg'
//            ]);
//            $attribute_size_options[4]->update([
//                'name' => 'xl',
//                'value' => 'xl'
//            ]);
//
//            $attribute_size_options->each(fn($option) =>
//                $product->product_attributes()->attach($attribute_size->id, ['product_attribute_option_id' => $option->id])
//            );
        }
    }

}
