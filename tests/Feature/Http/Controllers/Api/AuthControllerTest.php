<?php

namespace Tests\Feature\Http\Controllers\Api;

use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\AuthController
 */
class AuthControllerTest extends TestCase
{
    /**
     * @test
     */
    public function register_responds_with()
    {
        $response = $this->get(route('auth.register'));

        $response->assertNoContent();
    }


    /**
     * @test
     */
    public function login_responds_with()
    {
        $response = $this->get(route('auth.login'));

        $response->assertNoContent();
    }


    /**
     * @test
     */
    public function passwordReminder_responds_with()
    {
        $response = $this->get(route('auth.passwordReminder'));

        $response->assertNoContent();
    }
}
