<?php

namespace Database\Factories;

use App\Traits\IsTranslateableFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ProductAttribute;

class ProductAttributeFactory extends Factory
{
    use IsTranslateableFactory;

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
        $translations = $this->getTranslated($this->faker, ['name'], ['short']);

        return [
            'name' => $translations['name']
        ];
    }
}
