<?php

namespace Database\Factories;

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
    public function definition()
    {
        return [
            'user_id' => '',
            'name' => $this->faker->word,
            'address_line_1' => $this->faker->streetAddress,
            'address_line_2' => '',
            'post_code' => $this->faker->postcode,
            'town' => $this->faker->city,
            'lat' => $this->faker->latitude,
            'lon' => $this->faker->longitude,
        ];
    }
}
