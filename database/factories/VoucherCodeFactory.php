<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Models\VoucherCode;
use App\Enums\DiscountTypes;
use Illuminate\Database\Eloquent\Factories\Factory;

class VoucherCodeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = VoucherCode::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $valid_from = Carbon::createFromTimeStamp($this->faker->dateTimeBetween('-5 days', '+5 days')->getTimestamp());

        return [
            'type' => DiscountTypes::Percentage,
            'amount' => $this->faker->randomFloat(2, 1, 10),
            'valid_from' => $valid_from,
            'valid_until' => $valid_from->addDays($this->faker->randomElement(range(5,30))),
            'enabled' => true
        ];
    }
}
