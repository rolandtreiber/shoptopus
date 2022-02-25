<?php

namespace Database\Factories;

use App\Traits\TranslatableFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ProductAttribute;

class ProductAttributeFactory extends Factory
{
    use TranslatableFactory;

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductAttribute::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $translations = $this->getTranslated($this->faker, ['name'], ['word']);

        return [
            'name' => $translations['name'],
            'enabled' => $this->faker->boolean
        ];
    }
}
