<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\DeliveryType;
use App\Models\DeliveryRule;

class DeliveryRuleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DeliveryRule::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'delivery_type_id' => DeliveryType::factory(),
            'status' => $this->faker->numberBetween(-8, 8),
        ];
    }
}
