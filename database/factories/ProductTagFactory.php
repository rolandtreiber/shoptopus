<?php

namespace Database\Factories;

use App\Models\ProductTag;
use App\Traits\TranslatableFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductTagFactory extends Factory
{
    use TranslatableFactory;
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
            'description' => $translations['description'],
            'enabled' => $this->faker->boolean
        ];
    }
}
