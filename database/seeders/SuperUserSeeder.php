<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SuperUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superUser = User::factory()->state([
            'name' => config('shoptopus.super_user.name'),
            'email' => config('shoptopus.super_user.email'),
            'email_verified_at' => now(),
            'password' => Hash::make('Afgr45jh2238@'),
        ])->create();

        //$superUser = User::where('email', config('shoptopus.super_user.email'))->first();
        $superUser->assignRole(Role::findByName('super_admin'));
    }
}
