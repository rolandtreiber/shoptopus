<?php

namespace Database\Factories;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccessTokenFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $user = User::factory()->create();

        return [
            'type' => $this->faker->word(),
            'token' => $this->faker->regexify('[A-Za-z0-9]{120}'),
            'accessable_type' => User::class,
            'accessable_id' => $user->id,
            'user_id' => User::factory(),
            'issuer_user_id' => User::factory(),
            'expiry' => Carbon::now()->addDays(7)->toDateTimeString(),
        ];
    }
}
