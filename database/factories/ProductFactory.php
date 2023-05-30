<?php

namespace Database\Factories;

use App\Enums\ProductStatus;
use App\Enums\RandomStringMode;
use App\Helpers\GeneralHelper;
use App\Traits\TranslatableFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    use TranslatableFactory;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $translated = $this->getTranslated($this->faker, ['name', 'short_description', 'description', 'headline', 'subtitle'], ['short', 'medium', 'long', 'medium', 'medium']);

        $headline = null;
        $subtitle = null;
        if (env('APP_ENV') !== 'testing') {
            $headline = $translated['headline'];
            $subtitle = $translated['subtitle'];
        }

        return [
            'name' => $translated['name'],
            'short_description' => $translated['short_description'],
            'description' => $translated['description'],
            'price' => $this->faker->randomFloat(2, 1, 5000),
            'status' => ProductStatus::Active,
            'purchase_count' => $this->faker->numberBetween(0, 200),
            'stock' => $this->faker->numberBetween(1, 150),
            'backup_stock' => $this->faker->numberBetween(0, 150),
            'sku' => GeneralHelper::generateRandomString(10, RandomStringMode::UppercaseAndNumbers),
            'headline' => $headline,
            'subtitle' => $subtitle,
        ];
    }
}
