<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\PaymentSource;
use App\Enums\PaymentMethods;
use Illuminate\Database\Eloquent\Factories\Factory;

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
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->name,
            'source_id' => $this->faker->regexify('[A-Za-z0-9]{150}'),
            'exp_month' => $this->faker->regexify('[A-Za-z0-9]{2}'),
            'exp_year' => $this->faker->regexify('[A-Za-z0-9]{4}'),
            'last_four' => $this->faker->regexify('[A-Za-z0-9]{4}'),
            'brand' => $this->faker->regexify('[A-Za-z0-9]{50}'),
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
