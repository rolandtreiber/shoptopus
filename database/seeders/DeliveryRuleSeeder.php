<?php

namespace Database\Seeders;

use App\DeliveryRule;
use Illuminate\Database\Seeder;

class DeliveryRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DeliveryRule::factory()->count(5)->create();
    }
}
