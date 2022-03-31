<?php

namespace Database\Factories;

use App\Enums\FileType;
use App\Models\Product;
use App\Models\FileContent;
use App\Traits\TranslatableFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Relations\Relation;

class FileContentFactory extends Factory
{
    use TranslatableFactory;

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

        $fileable = $this->faker->randomElement([
            Product::class
        ]);

        return [
            'url' => 'https://picsum.photos/1200/1200',
            'fileable_type' => array_search($fileable, Relation::$morphMap),
            'fileable_id' => $fileable::factory(),
            'title' => $translated['title'],
            'file_name' => $this->faker->word,
            'description' => $translated['description'],
            'type' => FileType::Image
        ];
    }
}
