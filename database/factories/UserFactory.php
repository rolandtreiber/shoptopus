<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'email' => $this->faker->unique()->email,
            'prefix' => null,
//            'prefix' => $this->faker->randomElement(config('users.available_prefixes')),
            'email_verified_at' => null,
            'password' => bcrypt('password'),
            'temporary' => false,
            'phone' => null,
//            'phone' => $this->faker->phoneNumber,
            'avatar' => [
                'url' => 'https://picsum.photos/450/450',
                'file_name' => $this->faker->word
            ],
            'is_favorite' => false
        ];
    }
}
