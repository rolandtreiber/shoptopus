<?php

namespace Database\Factories;

use App\Enums\RandomStringMode;
use App\Helpers\GeneralHelper;
use App\Models\Product;
use App\Models\ProductVariant;
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
            'price' => $this->faker->randomFloat(2, 1, 5000),
            'product_id' => Product::factory(),
            'data' => null,
            'stock' => $this->faker->numberBetween(0, 30),
            'description' => $translations['description'],
            'sku' => GeneralHelper::generateRandomString(10, RandomStringMode::UppercaseAndNumbers),
            'enabled' => true,
        ];
    }
}
