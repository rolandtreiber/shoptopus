<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Product;

class ProductFactory extends Factory
{
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
        return [
            'name' => $this->faker->name,
            'price' => $this->faker->randomFloat(0, 0, 9999999999.),
            'status' => $this->faker->numberBetween(-8, 8),
            'purchase_count' => $this->faker->randomNumber(),
            'stock' => $this->faker->randomNumber(),
            'backup_stock' => $this->faker->randomNumber(),
        ];
    }
}
