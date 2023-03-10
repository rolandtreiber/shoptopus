<?php

namespace Database\Factories;

use App\Models\SystemSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

class SystemSettingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SystemSetting::class;

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
