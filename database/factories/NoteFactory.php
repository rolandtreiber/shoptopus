<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NoteFactory extends Factory
{
    public function definition(): array
    {
        return [
            "public" => $this->faker->boolean,
            "note" => "<p>".$this->faker->sentence."</p>",
            "user_id" => User::factory()
        ];
    }
}
