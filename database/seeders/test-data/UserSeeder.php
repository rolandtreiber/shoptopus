<?php

namespace Database\Seeders\TestData;

use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use function config;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws \Exception
     */
    public function run(): void
    {
        $users = [
            ['superadmin@m.com', 'Super Admin Mary'],
            ['admin@m.com', 'Admin Jane'],
            ['storemanager@m.com', 'Store Manager Alan'],
            ['storeassistant@m.com', 'Store Assistant Joe'],
            ['customer@m.com', 'Customer Lianne'],
            ['auditor@m.com', 'Auditor Bob'],
        ];
        $prefixes = config('users.available_prefixes');
        $faker = Factory::create();
        foreach ($users as $user) {
            User::factory()->state([
                'name' => $user[1],
                'email' => $user[0],
                'prefix' => $faker->randomElement($prefixes),
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('shop'),
                'created_at' => Carbon::now()->subDays(random_int(1, 30)),
            ])->create();
        }

        $users = User::all();
        $users[0]->assignRole(Role::findByName('super_admin'));
        $users[1]->assignRole(Role::findByName('admin'));
        $users[2]->assignRole(Role::findByName('store_manager'));
        $users[3]->assignRole(Role::findByName('store_assistant'));
        $users[3]->assignRole(Role::findByName('customer'));
        $users[5]->assignRole(Role::findByName('auditor'));
        User::factory()->count(15)->create();

        $customerRole = Role::findByName('customer');

        for ($i = 5; $i < 21; $i++) {
            (new User())->findNth($i)->assignRole($customerRole);
        }

        /** @var User $user */
        $user = User::factory()->state([
            'email' => 'rolandtreiber@gmail.com'
        ])->create();
        $user->assignRole(Role::findByName('super_admin'));
        $user->assignRole(Role::findByName('customer'));
    }
}

