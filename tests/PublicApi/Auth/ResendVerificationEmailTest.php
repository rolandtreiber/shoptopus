<?php

namespace Tests\PublicApi\Auth;

use App\Models\User;
use App\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ResendVerificationEmailTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();

        $this->user = User::factory()->create();
    }

    /**
     * @test
     *
     * @group apiPost
     */
    public function it_has_all_required_fields()
    {
        $data = ['email' => null];

        $this->sendRequest($data)
            ->assertJsonValidationErrors(['email']);

        Notification::assertNothingSent();
    }

    /**
     * @test
     *
     * @group apiPost
     */
    public function it_requires_an_existing_email_address()
    {
        $this->sendRequest(['email' => 'example@example.com'])
            ->assertJsonValidationErrors(['email']);

        Notification::assertNothingSent();
    }

    /**
     * @test
     *
     * @group apiPost
     */
    public function it_returns_a_success_response_if_the_email_has_already_been_verified()
    {
        $this->user->update(['email_verified_at' => now()]);

        $data = ['email' => $this->user->email];

        $this->assertEquals('Email has been verified.', $this->sendRequest($data)->json('data.message'));

        Notification::assertNothingSent();
    }

    /**
     * @test
     *
     * @group apiPost
     */
    public function it_resends_the_verification_email_if_unverified()
    {
        $data = ['email' => $this->user->email];

        $this->assertEquals(
            'Verification email re-sent.',
            $this->sendRequest($data)->json('data.message')
        );

        Notification::assertSentTo($this->user, VerifyEmail::class);

        Notification::assertSentTimes(VerifyEmail::class, 1);
    }

    protected function sendRequest($data = []): \Illuminate\Testing\TestResponse
    {
        return $this->postJson(route('verification.resend'), $data);
    }
}
