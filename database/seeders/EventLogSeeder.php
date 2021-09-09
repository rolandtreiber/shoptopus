<?php

namespace Database\Seeders;

use App\EventLog;
use Illuminate\Database\Seeder;

class EventLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        EventLog::factory()->count(5)->create();
    }
}
