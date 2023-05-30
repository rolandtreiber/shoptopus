<?php

namespace Tests\PublicApi\Auth;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SendPasswordResetTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     *
     * @group apiPost
     */
    public function authenticated_users_cannot_reset_their_password()
    {
        $this->signIn()
            ->sendRequest()
            ->assertRedirect();
    }

    /**
     * @test
     *
     * @group apiPost
     */
    public function it_has_all_required_fields()
    {
        $this->sendRequest(['email' => null])->assertJsonValidationErrors(['email']);
    }

    /**
     * @test
     *
     * @group apiPost
     */
    public function it_sends_the_reset_link_successfully()
    {
        Notification::fake();

        $user = User::factory()->create();

        $data = [
            'email' => $user->email,
        ];

        $this->assertDatabaseMissing('password_resets', ['email' => $user->email]);

        $res = $this->sendRequest($data)->json('data.message');

        $this->assertEquals('We have e-mailed your password reset link!', $res);

        $this->assertDatabaseHas('password_resets', [
            'email' => $user->email,
        ]);

        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    protected function sendRequest($data = []): \Illuminate\Testing\TestResponse
    {
        return $this->postJson(route('password.email'), $data);
    }
}
