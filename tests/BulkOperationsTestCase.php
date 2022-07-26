<?php

namespace Tests;

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Spatie\Permission\Models\Role;

class BulkOperationsTestCase extends TestCase
{
    public User $superAdmin;
    public User $storeManager;
    public User $storeAssistant;
    public User $customer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->superAdmin = $this->createUser(Role::findByName(UserRole::SuperAdmin));
        $this->storeManager = $this->createUser(Role::findByName(UserRole::StoreManager));
        $this->storeAssistant = $this->createUser(Role::findByName(UserRole::StoreAssistant));
        $this->customer = $this->createUser(Role::findByName(UserRole::Customer));
    }

    /**
     * @param Role $role
     * @return User
     */
    public function createUser(Role $role): User
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole($role);
        return $user;
    }

}
