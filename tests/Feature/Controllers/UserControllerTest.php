<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Tests\AdminControllerTestCase;

/**
 * @group users
 * @see \App\Http\Controllers\Admin\UserController
 */
class UserControllerTest extends AdminControllerTestCase
{
    /**
     * @test
     */
    public function test_can_list_users()
    {
        $systemUserIds = User::systemUsers()->pluck('id');
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->get(route('admin.api.index.users', [
            'page' => 1,
            'paginate' => 50
        ]));
        $response->assertOk();
        $users = $response->json()['data'];
        $this->assertCount(6, $response->json()['data']);
        foreach ($users as $user) {
            $this->assertContains($user['id'], $systemUserIds);
        }
    }

    /**
     * @test
     */
    public function test_can_show_user()
    {
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $systemUser = User::systemUsers()->first();
        $response = $this->get(route('admin.api.show.user', [
            'user' => $systemUser->id
        ]));
        $response->assertJsonFragment([
            "id" => $systemUser->id,
            "avatar" => [
                'url' => $systemUser->avatar->url,
                'file_name' => $systemUser->avatar->file_name
            ],
            "name" => $systemUser->name,
            "prefix" => $systemUser->prefix,
            "first_name" => $systemUser->first_name,
            "last_name" => $systemUser->last_name,
            "initials" => $systemUser->initials,
            "email" => $systemUser->email
        ]);
    }

    /**
     * @test
     */
    public function test_can_create_user()
    {
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->post(route('admin.api.create.user'), [
            'first_name' => 'Test',
            'last_name' => 'User',
            'prefix' => 'Mr.',
            'email' => 'hello@email.com',
            'roles' => ['store_manager']
        ]);
        $response->assertJsonFragment([
            'name' => 'Mr. Test User',
            'email' => 'hello@email.com'
        ]);
    }

    /**
     * @test
     */
    public function test_permissions_required_to_create_user()
    {
        $this->actingAs($this->getRandomNonSuperAdminOrStoreManager());
        $response = $this->post(route('admin.api.create.user'), [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'hello@email.com',
            'roles' => ['store_manager']
        ]);
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_can_update_user_as_super_admin()
    {
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $userToUpdateId = $this->getRandomNonSuperAdminOrStoreManager()->id;
        $response = $this->patch(route('admin.api.update.user', [
            'user' => $userToUpdateId
        ]), [
            'first_name' => 'Dolly',
            'last_name' => 'Parfait',
            'prefix' => 'Miss',
            'email' => 'dollyparfait@email.com',
            'roles' => ['store_assistant']
        ]);
        $response->assertOk();
        $response->assertJsonFragment([
            'name' => 'Miss Dolly Parfait',
            'email' => 'dollyparfait@email.com'
        ]);
        $user = User::find($userToUpdateId);
        $this->assertEquals('Dolly', $user->first_name);
        $this->assertEquals('Parfait', $user->last_name);
        $this->assertEquals('Miss Dolly Parfait', $user->name);
    }

    /**
     * @test
     */
    public function test_can_update_own_details()
    {
        $user = $this->getRandomNonSuperAdminOrStoreManager();
        $this->actingAs($user);
        $response = $this->patch(route('admin.api.update.user', [
            'user' => $user->id
        ]), [
            'first_name' => 'Dolly',
            'last_name' => 'Parfait',
            'prefix' => 'Miss',
            'email' => 'dollyparfait@email.com',
            'roles' => ['store_assistant']
        ]);
        $response->assertOk();
        $response->assertJsonFragment([
            'name' => 'Miss Dolly Parfait',
            'email' => 'dollyparfait@email.com'
        ]);
    }

    /**
     * @test
     */
    public function test_cannot_update_others_if_not_super_user()
    {
        $user = $this->getRandomNonSuperAdminOrStoreManager();
        $this->actingAs($user);
        do {
            $userToUpdate = $this->getRandomNonSuperAdminOrStoreManager();
        } while ($userToUpdate->id === $user->id);
        $response = $this->patch(route('admin.api.update.user', [
            'user' => $userToUpdate->id
        ]), [
            'first_name' => 'Dolly',
        ]);
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_can_delete_user_as_super_admin()
    {
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $userToUpdateId = $this->getRandomNonSuperAdminOrStoreManager()->id;
        $response = $this->delete(route('admin.api.delete.user', [
            'user' => $userToUpdateId
        ]));
        $response->assertOk();
        $response->assertJson(['status' => 'Success']);
    }

    /**
     * @test
     */
    public function test_cannot_delete_user_as_non_super_admin()
    {
        $user = $this->getRandomNonSuperAdminOrStoreManager();
        $this->actingAs($user);
        do {
            $userToDelete = $this->getRandomNonSuperAdminOrStoreManager();
        } while ($userToDelete->id === $user->id);
        $response = $this->delete(route('admin.api.delete.user', [
            'user' => $userToDelete->id
        ]));
        $response->assertForbidden();
    }
}
