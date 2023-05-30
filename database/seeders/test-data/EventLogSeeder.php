<?php

namespace Database\Seeders\TestData;

use App\EventLog;
use Illuminate\Database\Seeder;

class EventLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        EventLog::factory()->count(5)->create();
    }
}
