<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CartFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'ip_address' => null,
            //            'ip_address' => $this->faker->regexify('[A-Za-z0-9]{100}')
        ];
    }
}
