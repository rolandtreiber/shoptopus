<?php

namespace Database\Factories;

use App\Models\ProductCategory;
use App\Traits\TranslatableFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

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
        $translations = $this->getTranslated($this->faker, ['name', 'description'], ['word', 'medium']);

        return [
            'name' => $translations['name'],
            'description' => $translations['description'],
            'parent_id' => null,
            'enabled' => true
        ];
    }
}
