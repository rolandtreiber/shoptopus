<?php

namespace Database\Factories;

use App\Traits\TranslatableFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ProductCategory;

class ProductCategoryFactory extends Factory
{
    use TranslatableFactory;
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $translations = $this->getTranslated($this->faker, ['name', 'description'], ['short', 'medium']);
        return [
            'parent_id' => null,
            'name' => $translations['name'],
            'description' => $translations['description'],
            'enabled' => $this->faker->boolean
        ];
    }
}
