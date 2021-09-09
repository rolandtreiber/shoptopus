<?php

namespace Database\Seeders;

use App\PaymentSource;
use Illuminate\Database\Seeder;

class PaymentSourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PaymentSource::factory()->count(5)->create();
    }
}
