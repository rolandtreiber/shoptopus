<?php

namespace Database\Factories;

use App\Models\Product;
use App\Traits\IsTranslateableFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ProductVariant;

class ProductVariantFactory extends Factory
{
    use IsTranslateableFactory;

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
            'product_id' => 1,
            'data' => 'Some important data',
            'description' => $translations['description'],
            'stock' => $this->faker->numberBetween(0, 30),
            'price' => $this->faker->numberBetween(10, 50),
        ];
    }
}
