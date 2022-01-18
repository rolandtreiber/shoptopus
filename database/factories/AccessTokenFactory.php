<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\AccessToken;
use App\Models\User;

class AccessTokenFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AccessToken::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'tinyInteger' => $this->faker->word,
            'token' => $this->faker->regexify('[A-Za-z0-9]{120}'),
            'user_id' => User::factory(),
            'issuer_user_id' => User::factory(),
            'expiry' => $this->faker->dateTime(),
        ];
    }
}
