<?php

namespace Tests\Feature\AdminBaseCRUD;

use App\Exceptions\CannotDeleteRoleException;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\AdminControllerTestCase;

/**
 * @group roles-and-permissions
 */
class RoleAndPermissionControllerTest extends AdminControllerTestCase
{
    use RefreshDatabase;


    /** @test */
    public function test_roles_can_be_retrieved(): void
    {
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->get(route('admin.api.index.roles'));
        $response
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('data.0.name', 'super_admin')
                ->count('data', 7)
                ->etc());
    }

    /** @test */
    public function test_permissions_can_be_retrieved(): void
    {
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->get(route('admin.api.index.permissions'));
        $response
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('data.0.name', 'users.can.invite')
                ->count('data', 110)
                ->etc());
    }

    /** @test */
    public function test_permissions_can_be_retrieved_for_role(): void
    {
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->get(route('admin.api.show.role.permissions', [
            'role' => Role::findByName('super_admin')
        ]));
        $response
            ->assertJson(fn(AssertableJson $json) => $json
                ->count('data', 105)
                ->etc());
    }

    /** @test */
    public function test_users_can_be_retrieved_by_role(): void
    {
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->get(route('admin.api.show.role.users', [
            'role' => Role::findByName('store_manager')
        ]));
        $response
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('data.0.email', 'storemanager@m.com')
                ->where('data.0.roles.0', 'store_manager')
                ->count('data', 1)
                ->etc());
    }

    /** @test
     */
    public function test_role_can_be_created(): void
    {
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->post(route('admin.api.create.role', [
            'name' => 'content_editor'
        ]));
        $response
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('data.name', 'content_editor')
                ->etc());
    }

    /** @test
     */
    public function test_role_can_be_updated(): void
    {
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->patch(route('admin.api.update.role', [
            'role' => 3,
            'name' => 'UPDATED'
        ]));
        $response
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('data.id', 3)
                ->where('data.name', 'UPDATED')
                ->etc());
    }

    /** @test
     */
    public function test_role_can_be_deleted(): void
    {
        $role = new Role();
        $role->name = 'Test Role';
        $role->save();
        $roleId = $role->id;
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->delete(route('admin.api.delete.role', [
            'role' => $roleId,
        ]));
        $response->assertOk();
        $response
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('status', 'Success')
                ->etc());
        $this->assertDatabaseMissing('roles', [
            'id' => $roleId
        ]);
    }

    /** @test */
    public function test_permission_can_be_assigned_to_role(): void
    {
        $role = new Role();
        $role->name = 'Test Role';
        $role->save();
        $roleId = $role->id;
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $permission = Permission::findByName('users.can.update');
        $response = $this->post(route('admin.api.assign.permission.to.role', [
            'role' => $roleId,
            'permission' => $permission->id
        ]));
        $response->assertOk();
        $response
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('data.0.id', $permission->id)
                ->where('data.0.name', $permission->name)
                ->etc());
        $this->assertDatabaseHas('role_has_permissions', [
            'role_id' => $roleId,
            'permission_id' => $permission->id
        ]);
    }

    /** @test */
    public function test_permission_can_be_removed_from_role(): void
    {
        $role = Role::findByName('store_manager');
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $permission = Permission::findByName('users.can.update');
        $response = $this->delete(route('admin.api.remove.permission.from.role', [
            'role' => $role->id,
            'permission' => $permission->id
        ]));
        $response->assertOk();
        $response
            ->assertJson(fn(AssertableJson $json) => $json
                ->count('data', 70)
                ->etc());
        $this->assertDatabaseMissing('role_has_permissions', [
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
    }

    /** @test */
    public function test_role_can_be_assigned_to_user(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $role = new Role();
        $role->name = 'Test Role';
        $role->save();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->patch(route('admin.api.assign.role.to.user', [
            'role' => $role->id,
            'user' => $user->id
        ]));
        $response->assertOk();
        $response
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('data.0.email', $user->email)
                ->where('data.0.roles.0', 'Test Role')
                ->count('data', 1)
                ->etc());
    }

    /** @test */
    public function test_role_can_be_revoked_from_user(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $role = new Role();
        $role->name = 'Test Role';
        $role->save();
        $user->assignRole($role);
        $this->assertDatabaseHas('model_has_roles', [
            'role_id' => $role->id,
            'model_type' => User::class,
            'model_id' => $user->id
        ]);
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->delete(route('admin.api.remove.role.from.user', [
            'role' => $role->id,
            'user' => $user->id
        ]));
        $response->assertOk();
        $response
            ->assertJson(fn(AssertableJson $json) => $json
                ->count('data', 0)
                ->etc());
        $this->assertDatabaseMissing('model_has_roles', [
            'role_id' => $role->id,
            'model_type' => User::class,
            'model_id' => $user->id
        ]);
    }

    /** @test */
    public function test_only_super_user_can_retrieve_roles(): void
    {
        $this->actingAs(User::where('email', 'storemanager@m.com')->first());
        $response = $this->get(route('admin.api.index.roles'));
        $response->assertForbidden();
    }

    /** @test */
    public function test_only_super_user_can_retrieve_permissions(): void
    {
        $this->actingAs(User::where('email', 'storemanager@m.com')->first());
        $response = $this->get(route('admin.api.index.permissions'));
        $response->assertForbidden();
    }

    /** @test */
    public function test_only_super_user_can_retrieve_permissions_for_role(): void
    {
        $this->actingAs(User::where('email', 'storemanager@m.com')->first());
        $response = $this->get(route('admin.api.show.role.permissions', [
            'role' => Role::findByName('super_admin')
        ]));
        $response->assertForbidden();
    }

    /** @test */
    public function test_only_super_user_can_create_role(): void
    {
        $this->actingAs(User::where('email', 'storemanager@m.com')->first());
        $response = $this->post(route('admin.api.create.role', [
            'name' => 'content_editor'
        ]));
        $response->assertForbidden();
    }

    /** @test */
    public function test_only_super_user_can_update_role(): void
    {
        $this->actingAs(User::where('email', 'storemanager@m.com')->first());
        $response = $this->patch(route('admin.api.update.role', [
            'role' => 3,
            'name' => 'UPDATED'
        ]));
        $response->assertForbidden();
    }

    /** @test */
    public function test_only_super_user_can_delete_role(): void
    {
        $role = new Role();
        $role->name = 'Test Role';
        $role->save();
        $roleId = $role->id;
        $this->actingAs(User::where('email', 'storemanager@m.com')->first());
        $response = $this->delete(route('admin.api.delete.role', [
            'role' => $roleId,
        ]));
        $response->assertForbidden();
    }

    /** @test */
    public function test_only_super_user_can_assign_permission_to_role(): void
    {
        $role = new Role();
        $role->name = 'Test Role';
        $role->save();
        $roleId = $role->id;
        $this->actingAs(User::where('email', 'storemanager@m.com')->first());
        $permission = Permission::findByName('users.can.update');
        $response = $this->post(route('admin.api.assign.permission.to.role', [
            'role' => $roleId,
            'permission' => $permission->id
        ]));
        $response->assertForbidden();
    }

    /** @test */
    public function test_only_super_user_can_remove_permission_from_role(): void
    {
        $role = Role::findByName('store_manager');
        $this->actingAs(User::where('email', 'storemanager@m.com')->first());
        $permission = Permission::findByName('users.can.update');
        $response = $this->delete(route('admin.api.remove.permission.from.role', [
            'role' => $role->id,
            'permission' => $permission->id
        ]));
        $response->assertForbidden();
    }

    /** @test */
    public function test_role_cannot_be_deleted_if_assigned_to_a_user(): void
    {
        $role = Role::findByName('store_manager');
        $roleId = $role->id;
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->delete(route('admin.api.delete.role', [
            'role' => $roleId,
        ]));
        $response->assertStatus(500);
    }

    /** @test */
    public function test_super_admin_role_cannot_be_deleted(): void
    {
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->delete(route('admin.api.delete.role', [
            'role' => 1,
        ]));
        $response->assertStatus(500);
    }

    /** @test */
    public function test_only_super_admin_can_assign_role_to_users(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $role = new Role();
        $role->name = 'Test Role';
        $role->save();
        $this->actingAs(User::where('email', 'storemanager@m.com')->first());
        $response = $this->patch(route('admin.api.assign.role.to.user', [
            'role' => $role->id,
            'user' => $user->id
        ]));
        $response->assertForbidden();

    }

    /** @test */
    public function test_only_super_admin_can_revoke_role_from_users(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $role = new Role();
        $role->name = 'Test Role';
        $role->save();
        $user->assignRole($role);
        $this->assertDatabaseHas('model_has_roles', [
            'role_id' => $role->id,
            'model_type' => User::class,
            'model_id' => $user->id
        ]);
        $this->actingAs(User::where('email', 'storemanager@m.com')->first());
        $response = $this->delete(route('admin.api.remove.role.from.user', [
            'role' => $role->id,
            'user' => $user->id
        ]));
        $response->assertForbidden();
    }

}
