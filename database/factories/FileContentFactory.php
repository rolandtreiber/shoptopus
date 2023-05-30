<?php

namespace Database\Factories;

use App\Helpers\GeneralHelper;
use App\Models\Product;
use App\Traits\TranslatableFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Relations\Relation;

class FileContentFactory extends Factory
{
    use TranslatableFactory;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $translated = $this->getTranslated($this->faker, ['title', 'description'], ['short', 'medium']);

        $fileable = $this->faker->randomElement([
            Product::class,
        ]);

        $file = GeneralHelper::getPhotoFromSamples();

        return [
            'url' => $file['url'],
            'fileable_type' => array_search($fileable, Relation::$morphMap),
            'fileable_id' => $fileable::factory(),
            'title' => $translated['title'],
            'file_name' => $file['file_name'],
            'description' => $translated['description'],
            'type' => $file['type'],
        ];
    }
}
