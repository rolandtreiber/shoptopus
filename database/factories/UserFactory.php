<?php

namespace Database\Factories;

use App\Helpers\GeneralHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     *
     * @throws \Exception
     */
    public function definition(): array
    {
        $avatar = GeneralHelper::getPhotoFromSamples('avatars');

        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->email(),
            'prefix' => null,
            'email_verified_at' => null,
            'password' => bcrypt('password'),
            'temporary' => false,
            'phone' => null,
            'avatar' => [
                'url' => $avatar['url'],
                'file_name' => $avatar['file_name'],
            ],
            'is_favorite' => false,
            'last_seen' => Carbon::now()
        ];
    }
}
