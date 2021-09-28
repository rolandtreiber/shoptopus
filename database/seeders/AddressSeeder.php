<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\User;
use Exception;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        $customers = User::role('customer')->get();
        foreach ($customers as $customer) {
            Address::factory()->state([
                'user_id' => $customer->id
            ])->count(random_int(1, 2))->create();
        }
    }
}
