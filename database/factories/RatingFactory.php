<?php

namespace Database\Factories;

use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

class RatingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     *
     * @throws Exception
     */
    public function definition(): array
    {
        return [
            'rating' => random_int(1, 5),
            'description' => $this->faker->sentences(3, true),
            'title' => $this->faker->sentence(),
            'language_prefix' => $this->faker->randomElement(array_keys(config('app.locales_supported'))),
        ];
    }
}
