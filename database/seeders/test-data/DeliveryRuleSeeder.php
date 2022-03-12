<?php

namespace Database\Seeders\TestData;

use App\Models\DeliveryRule;
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
