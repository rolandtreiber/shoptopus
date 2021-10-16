<?php

namespace Database\Factories;

use App\Enums\DeliveryTypeStatuses;
use App\Traits\IsTranslateableFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\DeliveryType;

class DeliveryTypeFactory extends Factory
{
    use IsTranslateableFactory;

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
            'status' => $this->faker->randomElement([DeliveryTypeStatuses::Disabled, DeliveryTypeStatuses::Enabled]),
            'enabled_by_default_on_creation' => $this->faker->boolean,
            'price' => $this->faker->randomFloat(2, 0, 12)
        ];
    }
}
