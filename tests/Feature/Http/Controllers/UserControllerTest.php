<?php

namespace Tests\Feature\Http\Controllers;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\UserController
 */
class UserControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    /**
     * @test
     */
    public function index_responds_with()
    {
        $response = $this->get(route('user.index'));

        $response->assertNoContent();
    }


    /**
     * @test
     */
    public function update_uses_form_request_validation()
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\UserController::class,
            'update',
            \App\Http\Requests\UserUpdateRequest::class
        );
    }

    /**
     * @test
     */
    public function update_behaves_as_expected()
    {
        $user = User::factory()->create();
        $name = $this->faker->name;
        $email = $this->faker->safeEmail;

        $response = $this->put(route('user.update', $user), [
            'name' => $name,
            'email' => $email,
        ]);
    }


    /**
     * @test
     */
    public function store_uses_form_request_validation()
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\UserController::class,
            'store',
            \App\Http\Requests\UserStoreRequest::class
        );
    }

    /**
     * @test
     */
    public function store_behaves_as_expected()
    {
        $name = $this->faker->name;
        $email = $this->faker->safeEmail;

        $response = $this->post(route('user.store'), [
            'name' => $name,
            'email' => $email,
        ]);
    }


    /**
     * @test
     */
    public function destroy_deletes()
    {
        $user = User::factory()->create();

        $response = $this->delete(route('user.destroy', $user));

        $this->assertDeleted($user);
    }
}
