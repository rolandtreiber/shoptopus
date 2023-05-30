<?php

namespace Tests\PublicApi\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     *
     * @group apiPost
     */
    public function unauthenticated_users_cannot_logout(): void
    {
        $res = $this->sendRequest()->json();

        $this->assertEquals('Unauthenticated.', $res['developer_message']);
        $this->assertEquals('Sorry there was a system error, the administrator has been informed.', $res['user_message']);
    }

    /**
     * @test
     *
     * @group apiPost
     */
    public function authenticated_users_can_logout(): void
    {
        $this->signIn()
             ->sendRequest()
             ->assertSuccessful();
    }

    protected function sendRequest(): \Illuminate\Testing\TestResponse
    {
        return $this->postJson(route('api.auth.logout'));
    }
}
