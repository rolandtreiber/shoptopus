<?php

namespace Database\Factories;

use App\Traits\IsTranslateableFactory;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ProductAttributeOption;

class ProductAttributeOptionFactory extends Factory
{
    use IsTranslateableFactory;

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductAttributeOption::class;

    /**
     * Define the model's default state.
     *
     * @return array
     * @throws Exception
     */
    public function definition(): array
    {
        $translations = $this->getTranslated($this->faker, ['name'], ['word']);

        return [
            'name' => $translations['name'],
            'enabled' => $this->faker->boolean,
            'common_value' => random_int(1, 100)
        ];
    }
}
