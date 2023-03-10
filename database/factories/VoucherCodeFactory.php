<?php

namespace Database\Factories;

use App\Enums\DiscountType;
use App\Models\VoucherCode;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class VoucherCodeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $valid_from = Carbon::createFromTimeStamp($this->faker->dateTimeBetween('-5 days', '+5 days')->getTimestamp());

        return [
            'type' => DiscountType::Percentage,
            'amount' => $this->faker->randomFloat(2, 1, 10),
            'valid_from' => $valid_from->toDateTimeString(),
            'valid_until' => $valid_from->addDays($this->faker->randomElement(range(5, 30)))->toDateTimeString(),
            'enabled' => true,
        ];
    }
}
