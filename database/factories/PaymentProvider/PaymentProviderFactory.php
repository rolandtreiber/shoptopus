<?php

namespace Database\Factories\PaymentProvider;

use App\Models\PaymentProvider\PaymentProvider;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentProviderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PaymentProvider::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['paypal', 'amazon', 'stripe']),
            'enabled' => true,
            'test_mode' => true,
        ];
    }
}
