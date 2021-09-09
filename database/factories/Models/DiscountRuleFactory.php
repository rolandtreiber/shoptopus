<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\DiscountRule;

class DiscountRuleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DiscountRule::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'discountable_type' => $this->faker->word,
            'discountable_id' => $this->faker->randomNumber(),
            'type' => $this->faker->numberBetween(-8, 8),
            'amount' => $this->faker->randomFloat(0, 0, 9999999999.),
            'valid_from' => $this->faker->dateTime(),
            'valid_until' => $this->faker->dateTime(),
        ];
    }
}
