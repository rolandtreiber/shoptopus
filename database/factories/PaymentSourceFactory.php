<?php

namespace Database\Factories;

use App\Enums\PaymentMethod;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentSourceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     *
     * @throws \Exception
     */
    public function definition(): array
    {
        $brands = [
            'visa', 'mastercard', 'american express',
        ];

        return [
            'user_id' => User::factory(),
            'name' => $this->faker->words(2, true),
            'source_id' => $this->faker->regexify('[A-Za-z0-9]{150}'),
            'exp_month' => $this->faker->numberBetween(1, 12),
            'exp_year' => random_int(2022, 2025),
            'last_four' => random_int(1000, 9999),
            'brand' => $this->faker->randomElement($brands),
            'stripe_user_id' => $this->faker->regexify('[A-Za-z0-9]{120}'),
            'payment_method_id' => $this->faker->randomElement([
                PaymentMethod::Stripe,
                PaymentMethod::PayPal,
                PaymentMethod::ApplePay,
                PaymentMethod::GooglePay,
            ]),
        ];
    }
}
