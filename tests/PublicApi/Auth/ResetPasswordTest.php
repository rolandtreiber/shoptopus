<?php

namespace Tests\PublicApi\Auth;

use App\Models\PasswordReset;
use App\Models\User;
use App\Notifications\PasswordResetSuccess;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     *
     * @group apiPost
     */
    public function authenticated_users_cannot_update_their_password(): void
    {
        $this->signIn()->sendRequest()->assertRedirect();
    }

    /**
     * @test
     *
     * @group apiPost
     */
    public function it_has_all_required_fields(): void
    {
        $data = [
            'email' => null,
            'token' => null,
            'password' => null,
        ];

        $this->sendRequest($data)
            ->assertJsonValidationErrors(['email', 'token', 'password']);
    }

    /**
     * @test
     *
     * @group apiPost
     */
    public function it_updates_the_password_successfully(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $passwordReset = PasswordReset::factory()->create([
            'email' => $user->email,
        ]);

        $data = [
            'token' => $passwordReset->token,
            'email' => $user->email,
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ];

        $res = $this->sendRequest($data)->json('data.message');

        $this->assertEquals('Password successfully updated!', $res);

        $this->assertNull($passwordReset->fresh());

        Notification::assertSentTo($user, PasswordResetSuccess::class);
    }

    /**
     * @test
     *
     * @group apiPost
     */
    public function th_email_address_must_exists(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $passwordReset = PasswordReset::factory()->create([
            'email' => $user->email,
            'updated_at' => now()->subMinutes(61),
        ]);

        $data = [
            'token' => $passwordReset->token,
            'email' => 'someeial@not.com',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ];

        $this->assertEquals(
            'This password reset token is invalid.',
            $this->sendRequest($data)->json('developer_message')
        );

        $this->assertDatabaseHas('password_reset_tokens', ['email' => $user->email]);

        Notification::assertNotSentTo($user, PasswordResetSuccess::class);
    }

    /**
     * @test
     *
     * @group apiPost
     */
    public function the_token_expires_in_60_minutes(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $passwordReset = PasswordReset::factory()->create([
            'email' => $user->email,
            'updated_at' => now()->subMinutes(61),
        ]);

        $data = [
            'token' => $passwordReset->token,
            'email' => $user->email,
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ];

        $this->assertEquals(
            'This password reset token has expired.',
            $this->sendRequest($data)->json('developer_message')
        );

        $this->assertNull($passwordReset->fresh());

        Notification::assertNotSentTo($user, PasswordResetSuccess::class);
    }

    protected function sendRequest($data = []): \Illuminate\Testing\TestResponse
    {
        return $this->postJson(route('password.update'), $data);
    }
}
