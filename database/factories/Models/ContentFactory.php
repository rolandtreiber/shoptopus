<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Language;
use App\Models\Content;

class ContentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Content::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'contentable_type' => $this->faker->word,
            'contentable_id' => $this->faker->randomNumber(),
            'language_id' => Language::factory(),
            'type' => $this->faker->numberBetween(-8, 8),
            'text' => $this->faker->text,
        ];
    }
}
