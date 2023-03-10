<?php

namespace Database\Factories;

use App\Models\EventLog;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventLogFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EventLog::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'message' => $this->faker->regexify('[A-Za-z0-9]{200}'),
            'type' => $this->faker->numberBetween(-8, 8),
            'notification' => $this->faker->boolean,
            'user_id' => User::factory(),
            'actioned' => $this->faker->boolean,
            'data' => $this->faker->text,
        ];
    }
}
