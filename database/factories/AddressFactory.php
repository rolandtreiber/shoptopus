<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Address::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition() : array
    {
        return [
            'user_id' => User::factory(),
            'name' => null,
            'address_line_1' => $this->faker->streetAddress,
            'address_line_2' => null,
            'town' => $this->faker->city,
            'post_code' => $this->faker->postcode,
            'country' => 'UK',
            'lat' => $this->faker->latitude,
            'lon' => $this->faker->longitude,
        ];
    }
}
