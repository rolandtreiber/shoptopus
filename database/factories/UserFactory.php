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
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'role_id' => $this->faker->numberBetween(-8, 8),
            'email' => $this->faker->safeEmail,
            'email_verified_at' => $this->faker->dateTime(),
            'password' => Hash::make('shop'),
            'client_ref' => $this->faker->regexify('[A-Za-z0-9]{12}')
        ];
    }
}
