<?php

namespace Database\Factories;

use App\Enums\ProductAttributeType;
use App\Traits\TranslatableFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductAttributeFactory extends Factory
{
    use TranslatableFactory;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $translations = $this->getTranslated($this->faker, ['name'], ['word']);

        return [
            'name' => $translations['name'],
            'type' => ProductAttributeType::Text,
            'image' => null,
            'enabled' => true,
        ];
    }
}
