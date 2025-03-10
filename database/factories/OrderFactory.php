<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Address;
use App\Models\DeliveryType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $result = [
            'total_price' => $this->faker->numberBetween(100, 1000),
            'user_id' => User::factory(),
            'voucher_code_id' => null,
            'address_id' => Address::factory(),
            'currency_code' => 'GBP',
            'status' => OrderStatus::AwaitingPayment
        ];
        if (env('APP_ENV') === 'testing') {
            $result['delivery_type_id'] = null;
        } else {
            $result['delivery_type_id'] = DeliveryType::factory();
        }

        return $result;
    }
}
