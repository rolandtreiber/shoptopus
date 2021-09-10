<?php

namespace Database\Factories;

use App\Traits\IsTranslateableFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Log;

class ProductCategoryFactory extends Factory
{
    use IsTranslateableFactory;
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
            'description' => $translations['description']
        ];
    }
}
