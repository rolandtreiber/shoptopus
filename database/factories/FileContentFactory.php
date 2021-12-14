<?php

namespace Database\Factories;

use App\Traits\IsTranslateableFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\FileContent;

class FileContentFactory extends Factory
{
    use IsTranslateableFactory;

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
        $translated = $this->getTranslated($this->faker, ['title', 'description'], ['short', 'medium']);

        return [
            'url' => $this->faker->imageUrl(1200, 1200),
            'fileable_type' => $this->faker->word,
            'fileable_id' => $this->faker->randomNumber(),
            'title' => $translated['title'],
            'file_name' => $this->faker->word,
            'description' => $translated['description'],
        ];
    }
}
