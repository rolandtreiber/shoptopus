<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductTag;
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
            ProductTag::factory()->count(2)->create()->each(function ($tag) use ($product) {
                $tag->products()->attach($product->id);
            });

            // Product Category
            $parent_category = ProductCategory::factory()->create(['name' => 'Women']);
            $product_category = ProductCategory::factory()->create([
                'name' => 'T-shirts',
                'parent_id' => $parent_category->id
            ]);
            $parent_category->products()->attach($product->id);
            $product_category->products()->attach($product->id);

            // Product Attributes
            // Color
            $attribute_color = ProductAttribute::factory()->create([
                'type' => ProductAttributeType::Color,
                'name' => 'color'
            ]);

            $attribute_color_options = ProductAttributeOption::factory()
                ->count(3)
                ->create([
                    'product_attribute_id' => $attribute_color->id
                ]);

            $attribute_color_options[0]->update([
                'name' => 'red',
                'value' => 'rgb(255, 0, 0)'
            ]);
            $attribute_color_options[1]->update([
                'name' => 'blue',
                'value' => 'rgb(0, 0, 255)'
            ]);
            $attribute_color_options[2]->update([
                'name' => 'green',
                'value' => 'rgb(0, 255, 0)'
            ]);

            $product->product_attributes()->attach($attribute_color->id);

            // Size
            $attribute_size = ProductAttribute::factory()->create([
                'type' => ProductAttributeType::Text,
                'name' => 'size'
            ]);

            $attribute_size_options = ProductAttributeOption::factory()
                ->count(5)
                ->create([
                    'product_attribute_id' => $attribute_size->id
                ]);

            $attribute_size_options[0]->update([
                'name' => 'extra small',
                'value' => 'xs'
            ]);
            $attribute_size_options[1]->update([
                'name' => 'small',
                'value' => 'sm'
            ]);
            $attribute_size_options[2]->update([
                'name' => 'medium',
                'value' => 'md'
            ]);
            $attribute_size_options[3]->update([
                'name' => 'large',
                'value' => 'lg'
            ]);
            $attribute_size_options[4]->update([
                'name' => 'extra large',
                'value' => 'xl'
            ]);

            $product->product_attributes()->attach($attribute_size->id);
        }
    }

}
