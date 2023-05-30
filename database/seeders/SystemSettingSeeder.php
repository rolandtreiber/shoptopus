<?php

namespace Database\Seeders;

use App\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SystemSetting::factory()->count(5)->create();
    }
}
