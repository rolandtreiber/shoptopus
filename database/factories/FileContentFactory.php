<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\FileContent;

class FileContentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FileContent::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'url' => $this->faker->url,
            'fileable_type' => $this->faker->word,
            'fileable_id' => $this->faker->randomNumber(),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->text,
        ];
    }
}
