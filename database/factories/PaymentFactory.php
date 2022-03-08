<?php

namespace Database\Factories;

use App\Enums\PaymentTypes;
use App\Enums\RandomStringModes;
use App\Helpers\GeneralHelper;
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
    public function definition(): array
    {
        return [
            'payable_type' => $this->faker->word,
            'payable_id' => $this->faker->randomNumber(),
            'payment_source_id' => PaymentSource::factory(),
            'amount' => $this->faker->numberBetween(100, 1000),
            'user_id' => User::factory(),
            'status' => $this->faker->numberBetween(-8, 8),
            'payment_ref' => GeneralHelper::generateRandomString(10, RandomStringModes::UppercaseLowercaseAndNumbers),
            'method_ref' => GeneralHelper::generateRandomString(10, RandomStringModes::UppercaseLowercaseAndNumbers),
            'type' => PaymentTypes::Payment,
            'description' => $this->faker->text,
        ];
    }
}
