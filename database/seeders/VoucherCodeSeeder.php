<?php

namespace Database\Seeders;

use App\Models\VoucherCode;
use Illuminate\Database\Seeder;

class VoucherCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        VoucherCode::factory()->count(5)->create();
    }
}
