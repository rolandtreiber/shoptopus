<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class SuperUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $superUser = User::factory()->state([
            'name' => config('shoptopus.super_user.name'),
            'email' => config('shoptopus.super_user.email'),
            'email_verified_at' => now(),
            'password' => Hash::make('shop')
        ])->create();

        //$superUser = User::where('email', config('shoptopus.super_user.email'))->first();
        $superUser->assignRole(Role::findByName('super_admin'));
    }
}
