<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Enums\DiscountType;
use App\Models\DiscountRule;
use App\Traits\TranslatableFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

class DiscountRuleFactory extends Factory
{
    use TranslatableFactory;

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

        $valid_from = Carbon::createFromTimeStamp($this->faker->dateTimeBetween('-5 days', '+5 days')->getTimestamp());

        return [
            'type' => $this->faker->randomElement([DiscountType::Amount, DiscountType::Percentage]),
            'amount' => $this->faker->randomFloat(2, 0, 50),
            'name' => $translations['name'],
            'valid_from' => $valid_from->toDateTimeString(),
            'valid_until' => $valid_from->addDays($this->faker->randomElement(range(5,30)))->toDateTimeString(),
        ];
    }
}
