<?php

namespace Database\Factories;

use App\Enums\OrderStatuses;
use App\Models\Address;
use App\Models\DeliveryType;
use App\Models\Order;
use App\Models\User;
use App\Models\VoucherCode;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'delivery_type_id' => DeliveryType::factory(),
            'voucher_code_id' => VoucherCode::factory(),
            'address_id' => Address::factory(),
            'original_price' => $this->faker->numberBetween(100, 1000),
            'subtotal' => $this->faker->numberBetween(100, 1000),
            'total_price' => $this->faker->numberBetween(100, 1000),
            'total_discount' => $this->faker->numberBetween(10, 20),
            'delivery' => $this->faker->numberBetween(5, 10),
            'status' => $this->faker->randomElement([
                OrderStatuses::Processing,
                OrderStatuses::OnHold,
                OrderStatuses::Paid,
                OrderStatuses::Completed,
                OrderStatuses::Cancelled,
                OrderStatuses::InTransit
            ]),
        ];
    }
}
