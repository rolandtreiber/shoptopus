<?php

namespace Database\Factories;

use App\Models\Product;
use App\Helpers\GeneralHelper;
use App\Models\ProductVariant;
use App\Enums\RandomStringMode;
use App\Traits\TranslatableFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductVariantFactory extends Factory
{
    use TranslatableFactory;

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductVariant::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $translations = $this->getTranslated($this->faker, ['description'], ['medium']);

        return [
            'product_id' => Product::factory(),
            'data' => 'Some important data',
            'description' => $translations['description'],
            'stock' => $this->faker->numberBetween(0, 30),
            'price' => $this->faker->numberBetween(10, 50),
            'sku' => GeneralHelper::generateRandomString(10, RandomStringMode::UppercaseAndNumbers)
        ];
    }
}
