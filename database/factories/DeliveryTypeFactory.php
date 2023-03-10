<?php

namespace Database\Factories;

use App\Models\DeliveryType;
use App\Traits\TranslatableFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeliveryTypeFactory extends Factory
{
    use TranslatableFactory;

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DeliveryType::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $translations = $this->getTranslated($this->faker, ['name', 'description'], ['word', 'medium']);

        return [
            'name' => $translations['name'],
            'description' => $translations['description'],
            'enabled' => true,
            'enabled_by_default_on_creation' => true,
            'price' => $this->faker->randomFloat(2, 0, 12),
        ];
    }
}
