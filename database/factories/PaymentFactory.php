<?php

namespace Database\Factories;

use App\Enums\PaymentType;
use App\Enums\RandomStringMode;
use App\Helpers\GeneralHelper;
use App\Models\Order;
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
        $order = Order::factory()->create();

        return [
            'payable_type' => Order::class,
            'payable_id' => $order->id,
            'payment_source_id' => PaymentSource::factory(),
            'amount' => $this->faker->numberBetween(100, 1000),
            'user_id' => User::factory(),
            'status' => $this->faker->numberBetween(-8, 8),
            'payment_ref' => GeneralHelper::generateRandomString(10, RandomStringMode::UppercaseLowercaseAndNumbers),
            'method_ref' => GeneralHelper::generateRandomString(10, RandomStringMode::UppercaseLowercaseAndNumbers),
            'type' => PaymentType::Payment,
            'description' => $this->faker->text,
        ];
    }
}
