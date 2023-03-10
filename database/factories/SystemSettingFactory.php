<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SystemSettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'key' => $this->faker->regexify('[A-Za-z0-9]{60}'),
            'value' => $this->faker->text(),
            'type' => $this->faker->numberBetween(-8, 8),
        ];
    }
}
