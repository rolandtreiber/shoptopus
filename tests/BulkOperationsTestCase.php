<?php

namespace Tests;

use App\Enums\UserRoles;
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
        $this->superAdmin = $this->createUser(Role::findByName(UserRoles::SuperAdmin));
        $this->storeManager = $this->createUser(Role::findByName(UserRoles::StoreManager));
        $this->storeAssistant = $this->createUser(Role::findByName(UserRoles::StoreAssistant));
        $this->customer = $this->createUser(Role::findByName(UserRoles::Customer));
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
