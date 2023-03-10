<?php

namespace Database\Factories;

use App\Models\ProductAttributeOption;
use App\Traits\TranslatableFactory;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductAttributeOptionFactory extends Factory
{
    use TranslatableFactory;

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
     *
     * @throws Exception
     */
    public function definition(): array
    {
        $translations = $this->getTranslated($this->faker, ['name'], ['word']);

        return [
            'name' => $translations['name'],
            'value' => random_int(1, 100),
            'image' => null,
            'enabled' => true,
        ];
    }
}
