<?php

namespace Database\Factories;

use App\Language;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'contentable_type' => $this->faker->word(),
            'contentable_id' => $this->faker->randomNumber(),
            'language_id' => Language::factory(),
            'type' => $this->faker->numberBetween(-8, 8),
            'text' => $this->faker->text(),
        ];
    }
}
