<?php

namespace Database\Factories;

use App\Models\DeliveryRule;
use App\Models\DeliveryType;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeliveryRuleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DeliveryRule::class;

    /**
     * Define the model's default state.
     *
     * @return array
     *
     * @throws \Exception
     */
    public function definition()
    {
        $postcodes = [];
        $postcodeCount = random_int(0, 10);
        for ($i = 0; $i < $postcodeCount; $i++) {
            $postcodes[] = $this->faker->postcode;
        }

        $noMinWeight = $this->faker->boolean;
        $minWeight = $noMinWeight ? 0 : $this->faker->randomNumber(4);
        $maxWeight = $minWeight + 5000;

        $noMinDistance = $this->faker->boolean;
        $minDistance = $noMinDistance ? 0 : $this->faker->randomFloat(10, 100);
        $maxDistance = $minWeight + 50;

        return [
            'delivery_type_id' => DeliveryType::factory(),
            'postcodes' => $postcodes,
            'min_weight' => $minWeight,
            'max_weight' => $maxWeight,
            'min_distance' => $minDistance,
            'max_distance' => $maxDistance,
            'distance_unit' => 'mile',
            'lat' => $this->faker->latitude,
            'lon' => $this->faker->longitude,
            'enabled' => true,
        ];
    }
}
