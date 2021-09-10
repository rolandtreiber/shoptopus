<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

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
            ['superadmin@m.com', 'Super Admin Mary'],
            ['admin@m.com', 'Admin Jane'],
            ['storemanager@m.com', 'Store Manager Alan'],
            ['storeassistant@m.com', 'Store Assistant Joe'],
            ['customer@m.com', 'Customer Lianne'],
        ];

        foreach ($users as $user) {
            User::factory()->state([
                'name' => $user[1],
                'email' => $user[0],
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('shop')
            ])->create();
        }

        User::find(1)->assignRole(Role::findByName('super_admin'));
        User::find(2)->assignRole(Role::findByName('admin'));
        User::find(3)->assignRole(Role::findByName('store_manager'));
        User::find(4)->assignRole(Role::findByName('store_assistant'));
        User::find(4)->assignRole(Role::findByName('customer'));
        User::factory()->count(15)->create();

        $customerRole = Role::findByName('customer');

        for ($i = 5; $i < 21; $i++) {
            User::find($i)->assignRole($customerRole);
        }

    }
}

