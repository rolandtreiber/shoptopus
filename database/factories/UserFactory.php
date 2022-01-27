<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     * @throws \Exception
     */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'prefix' => $this->faker->randomElement(config('users.available_prefixes')),
            'email' => $this->faker->unique()->email,
            'email_verified_at' => $this->faker->dateTime(),
            'password' => Hash::make('shop'),
            'temporary' => false,
            'phone' => $this->faker->phoneNumber,
            'avatar' => [
                'url' => 'https://picsum.photos/450/450',
                'file_name' => $this->faker->word
            ],
            'is_favorite' => $this->faker->boolean
        ];
    }
}
