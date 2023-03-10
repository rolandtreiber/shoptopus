<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'address_line_1' => str_replace("\n", ' ', $this->faker->streetAddress()),
            'town' => $this->faker->city(),
            'post_code' => $this->faker->postcode(),
            'country' => 'UK',
            'user_id' => User::factory(),
            'name' => null,
            'address_line_2' => null,
            'lat' => null,
            'lon' => null,
        ];
    }
}
