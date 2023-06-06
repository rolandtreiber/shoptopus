<?php

namespace Database\Factories\PaymentProvider;

use App\Models\PaymentProvider\PaymentProvider;
use App\Models\PaymentProvider\PaymentProviderConfig;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentProviderConfigFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PaymentProviderConfig::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'setting' => $this->faker->word(),
            'value' => $this->faker->text(),
            'test_value' => $this->faker->text(),
            'payment_provider_id' => PaymentProvider::factory(),
        ];
    }
}
