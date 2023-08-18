<?php

namespace Tests\PublicApi\Auth;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/**
 * @group password-reset
 */
class SendPasswordResetTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     *
     * @group apiPost
     */
    public function authenticated_users_cannot_reset_their_password(): void
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
    public function it_has_all_required_fields(): void
    {
        $this->sendRequest(['email' => null])->assertJsonValidationErrors(['email']);
    }

    /**
     * @test
     *
     * @group apiPost
     */
    public function it_sends_the_reset_link_successfully(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $data = [
            'email' => $user->email,
        ];

        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->email]);

        $res = $this->sendRequest($data)->json('data.message');

        $this->assertEquals('If the email is in our system, we have e-mailed your password reset link!', $res);

        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => $user->email,
        ]);

        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    protected function sendRequest($data = []): \Illuminate\Testing\TestResponse
    {
        return $this->postJson(route('password.email'), $data);
    }
}
