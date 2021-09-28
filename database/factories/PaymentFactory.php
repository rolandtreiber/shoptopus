<?php

namespace Database\Factories;

use App\Models\PaymentSource;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Payment;

class PaymentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'payable_type' => $this->faker->word,
            'payable_id' => $this->faker->randomNumber(),
            'payment_source_id' => PaymentSource::factory(),
            'user_id' => User::factory(),
            'decimal' => $this->faker->word,
            'status' => $this->faker->numberBetween(-8, 8),
            'payment_ref' => $this->faker->regexify('[A-Za-z0-9]{150}'),
            'method_ref' => $this->faker->regexify('[A-Za-z0-9]{150}'),
            'type' => $this->faker->numberBetween(-8, 8),
            'description' => $this->faker->text,
        ];
    }
}
