<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\PasswordReset;
use Illuminate\Database\Eloquent\Factories\Factory;

class PasswordResetFactory extends Factory
{

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PasswordReset::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition() : array
    {
        return [
            'email' => $this->faker->unique()->safeEmail,
            'token' => Str::random(60)
        ];
    }
}
