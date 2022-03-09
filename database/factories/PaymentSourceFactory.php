<?php

namespace Database\Factories;

use App\Enums\PaymentMethods;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PaymentSource;

class PaymentSourceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PaymentSource::class;

    /**
     * Define the model's default state.
     *
     * @return array
     * @throws \Exception
     */
    public function definition(): array
    {
        $brands = [
            'visa', 'mastercard', 'american express'
        ];

        return [
            'user_id' => User::factory(),
            'name' => $this->faker->words(2, true),
            'source_id' => $this->faker->regexify('[A-Za-z0-9]{150}'),
            'exp_month' => $this->faker->numberBetween(1, 12),
            'exp_year' => random_int(2021, 2025),
            'last_four' => random_int(1000, 9999),
            'brand' => $this->faker->randomElement($brands),
            'stripe_user_id' => $this->faker->regexify('[A-Za-z0-9]{120}'),
            'payment_method_id' => $this->faker->randomElement([
                PaymentMethods::Stripe,
                PaymentMethods::PayPal,
                PaymentMethods::ApplePay,
                PaymentMethods::GooglePay
            ]),
        ];
    }
}
