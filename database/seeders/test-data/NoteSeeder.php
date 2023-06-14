<?php

namespace Database\Seeders\TestData;

use App\Enums\UserRole;
use App\Models\Note;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class NoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRoles = [
            Role::findByName(UserRole::Admin)->id,
            Role::findByName(UserRole::SuperAdmin)->id,
            Role::findByName(UserRole::Seller)->id,
            Role::findByName(UserRole::StoreAssistant)->id,
            Role::findByName(UserRole::StoreManager)->id
        ];
        $adminUserIds = DB::table('users')
            ->join('model_has_roles', 'users.id', 'model_has_roles.model_id')
            ->where('model_has_roles.model_type', User::class)
            ->whereIn('model_has_roles.role_id', $adminRoles)
            ->pluck('id')->toArray();

        $customerUserIds = DB::table('users')
            ->join('model_has_roles', 'users.id', 'model_has_roles.model_id')
            ->where('model_has_roles.model_type', User::class)
            ->where('model_has_roles.role_id', Role::findByName(UserRole::Customer)->id)
            ->pluck('id')->toArray();

        $orderIds = DB::table('orders')->pluck('id');

        for ($i = 0; $i < 4; $i++) {
            foreach ($orderIds as $orderId) {
                if (random_int(1, 100) < 50) {
                    Note::factory()->state([
                        'noteable_id' => $orderId,
                        'noteable_type' => Order::class,
                        'user_id' => $adminUserIds[random_int(0, count($adminUserIds) - 1)]
                    ])->create();
                }
            }
        }

        for ($i = 0; $i < 4; $i++) {
            foreach ($customerUserIds as $customerUserId) {
                if (random_int(1, 100) < 50) {
                    Note::factory()->state([
                        'noteable_id' => $customerUserId,
                        'noteable_type' => User::class,
                        'user_id' => $adminUserIds[random_int(0, count($adminUserIds) - 1)]
                    ])->create();
                }
            }
        }
    }
}
