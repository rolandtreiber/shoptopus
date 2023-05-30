<?php

namespace Database\Seeders\TestData;

use App\Models\Address;
use App\Models\User;
use Exception;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Foundation\Testing\WithFaker;

class AddressSeeder extends Seeder
{
    use WithFaker;

    /**
     * Run the database seeds.
     *
     *
     * @throws Exception
     */
    public function run(): void
    {
        $faker = Factory::create();
        $customers = User::role('customer')->get();
        foreach ($customers as $customer) {
            Address::factory()->state([
                'user_id' => $customer->id,
                'lat' => $faker->latitude(),
                'lon' => $faker->longitude(),
            ])->count(random_int(1, 2))->create();
        }
    }
}
