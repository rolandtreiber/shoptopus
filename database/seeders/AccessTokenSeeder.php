<?php

namespace Database\Seeders;

use App\AccessToken;
use Illuminate\Database\Seeder;

class AccessTokenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AccessToken::factory()->count(5)->create();
    }
}
