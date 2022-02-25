<?php

namespace Database\Factories;

use App\Enums\ProductStatuses;
use App\Enums\RandomStringModes;
use App\Helpers\GeneralHelper;
use App\Traits\TranslatableFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;

class ProductFactory extends Factory
{
    use TranslatableFactory;

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $translated = $this->getTranslated($this->faker, ['name', 'short_description', 'description'], ['short', 'medium', 'long']);

        return [
            'name' => $translated['name'],
            'short_description' => $translated['short_description'],
            'description' => $translated['description'],
            'price' => $this->faker->numberBetween(10, 55),
            'status' => $this->faker->randomElement([ProductStatuses::Active, ProductStatuses::Provisional, ProductStatuses::Discontinued]),
            'purchase_count' => $this->faker->numberBetween(0, 200),
            'stock' => $this->faker->numberBetween(0, 150),
            'backup_stock' => $this->faker->numberBetween(0, 150),
            'sku' => GeneralHelper::generateRandomString(10, RandomStringModes::UppercaseAndNumbers)
        ];
    }
}
