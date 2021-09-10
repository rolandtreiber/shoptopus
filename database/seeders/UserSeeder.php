<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $users = [
            ['u1@mail.com', 'Test User 1'],
            ['u2@mail.com', 'Test User 2'],
            ['u3@mail.com', 'Test User 3'],
            ['u4@mail.com', 'Test User 4'],
            ['u5@mail.com', 'Test User 5'],
        ];

        foreach ($users as $user) {
            User::factory()->state([
                'name' => $user[1],
                'email' => $user[0],
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('shop')
            ])->create();
        }

        User::factory()->count(15)->create();

    }
}

