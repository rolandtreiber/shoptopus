<?php

namespace Database\Factories;

use App\Models\Rating;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

class RatingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Rating::class;

    /**
     * Define the model's default state.
     *
     * @return array
     *
     * @throws Exception
     */
    public function definition(): array
    {
        return [
            'rating' => random_int(1, 5),
            'description' => $this->faker->sentences(3, true),
            'title' => $this->faker->sentence,
            'language_prefix' => array_keys($this->faker->randomElement(config('app.locales_supported')))[0],
        ];
    }
}
