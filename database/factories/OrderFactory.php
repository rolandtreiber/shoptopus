<?php

namespace Database\Factories;

use App\Models\DeliveryType;
use App\Models\User;
use App\Models\Order;
use App\Models\Address;
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
    public function definition() : array
    {
        $result = [
            'total_price' => $this->faker->numberBetween(100, 1000),
            'user_id' => User::factory(),
            'voucher_code_id' => null,
            'address_id' => Address::factory(),
            'currency_code' => 'GBP'
        ];
        if (env('APP_ENV') === "testing") {
            $result['delivery_type_id'] = null;
        } else {
            $result['delivery_type_id'] = DeliveryType::factory();
        }
        return $result;
    }
}
