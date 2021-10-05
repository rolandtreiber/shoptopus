<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SuperUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->state([
            'name' => config('shoptopus.super_user.name'),
            'email' => config('shoptopus.super_user.email'),
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('shop')
        ])->create();

        $superUser = User::where('email', config('shoptopus.super_user.email'))->first();
        $superUser->assignRole(Role::findByName('super_admin'));
    }
}
