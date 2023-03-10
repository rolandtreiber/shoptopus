<?php

namespace Database\Factories;

use App\Enums\PaymentType;
use App\Enums\RandomStringMode;
use App\Helpers\GeneralHelper;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'payable_id' => Order::factory(),
            'payable_type' => function (array $attributes) {
                return get_class(Order::find($attributes['payable_id']));
            },
            'user_id' => null,
            'payment_source_id' => null,
            //            'payment_source_id' => PaymentSource::factory(),
            'amount' => $this->faker->randomFloat(2, 1, 20),
            //            'user_id' => User::factory(),
            'proof' => null,
            'status' => $this->faker->numberBetween(-8, 8),
            'payment_ref' => GeneralHelper::generateRandomString(10, RandomStringMode::UppercaseLowercaseAndNumbers),
            'method_ref' => GeneralHelper::generateRandomString(10, RandomStringMode::UppercaseLowercaseAndNumbers),
            'type' => PaymentType::Payment,
            'description' => $this->faker->text(),
        ];
    }
}
