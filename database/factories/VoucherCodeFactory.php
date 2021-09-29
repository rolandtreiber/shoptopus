<?php

namespace Database\Factories;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\VoucherCode;

class VoucherCodeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = VoucherCode::class;

    /**
     * Define the model's default state.
     *
     * @return array
     * @throws Exception
     */
    public function definition(): array
    {
        $validityBasis = Carbon::now()->addDays(random_int(-5, 5));
        $validityLength = random_int(5, 30);
        $expiryDate = Carbon::parse($validityBasis)->addDays($validityLength);

        return [
            'code' => '',
            'valid_from' => $validityBasis,
            'valid_until' => $expiryDate,
            'amount' => random_int(1, 15),
            'type' => random_int(0, 1)
        ];
    }
}
