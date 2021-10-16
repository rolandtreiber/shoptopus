<?php

namespace Database\Factories;

use App\Enums\DiscountTypes;
use App\Traits\IsTranslateableFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\DiscountRule;

class DiscountRuleFactory extends Factory
{
    use IsTranslateableFactory;

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
        $translations = $this->getTranslated($this->faker, ['name'], ['short']);

        return [
            'type' => $this->faker->randomElement([DiscountTypes::Amount, DiscountTypes::Percentage]),
            'amount' => $this->faker->randomFloat(2, 0, 50),
            'name' => $translations['name'],
            'valid_from' => $this->faker->dateTime(),
            'valid_until' => $this->faker->dateTime(),
        ];
    }
}
