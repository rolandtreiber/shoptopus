<?php

namespace Database\Factories;

use App\Traits\IsTranslateableFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ProductTag;

class ProductTagFactory extends Factory
{
    use IsTranslateableFactory;
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductTag::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $translations = $this->getTranslated($this->faker, ['name', 'description'], ['word', 'medium']);
        return [
            'name' => $translations['name'],
            'description' => $translations['description']
        ];
    }
}
