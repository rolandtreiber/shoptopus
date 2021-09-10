<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\PaymentSource;
use App\User;

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
    public function definition()
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
            'payment_method_id' => $this->faker->numberBetween(-8, 8),
        ];
    }
}
