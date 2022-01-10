<?php

namespace Tests\PublicApi\Auth;

use Tests\TestCase;
use App\Models\User;
use App\Events\UserSignedUp;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SignUpTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @group apiPost
     */
    public function it_has_all_required_fields()
    {
        Notification::fake();

        $data = [
            'first_name' => null,
            'last_name' => null,
            'email' => null,
            'password' => null
        ];

        $this->sendRequest($data)
            ->assertJsonValidationErrors(['first_name', 'last_name', 'email', 'password']);

        $this->assertDatabaseMissing('users', $data);

        Notification::assertNothingSent();
    }

    /**
     * @test
     * @group apiPost
     */
    public function it_requires_a_valid_email()
    {
        $data = [
            'first_name' => "Istvan",
            'last_name' => "Lovas",
            'email' => "lolevesgmail",
            'password' => "password",
            'password_confirmation' => "password"
        ];

        $this->sendRequest($data)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * @test
     * @group apiPost
     */
    public function it_requires_a_password_confirmation()
    {
        $data = [
            'first_name' => "Istvan",
            'last_name' => "Lovas",
            'email' => "loleves@gmail.com",
            'password' => "password"
        ];

        $this->sendRequest($data)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * @test
     * @group apiPost
     */
    public function the_password_has_a_minimum_number_of_characters()
    {
        $data = [
            'first_name' => "Istvan",
            'last_name' => "Lovas",
            'email' => "loleves@gmail.com",
            'password' => "passwor",
            'password_confirmation' => "passwor"
        ];

        $this->sendRequest($data)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * @test
     * @group apiPost
     */
    public function it_saves_the_user_to_the_database()
    {
        $this->artisan('passport:install');

        $data = [
            'first_name' => "Istvan",
            'last_name' => "Lovas",
            'email' => "loleves@gmail.com",
            'password' => "password",
            'password_confirmation' => "password"
        ];

        $this->sendRequest($data)->assertOk();

        $this->assertDatabaseHas('users', [
            'first_name' => "Istvan",
            'last_name' => "Lovas",
            'email' => "loleves@gmail.com"
        ]);
    }

    /**
     * @test
     * @group apiPost
     */
    public function it_dispatches_the_correct_event()
    {
        Event::fake();

        $this->artisan('passport:install');

        $data = [
            'first_name' => "Istvan",
            'last_name' => "Lovas",
            'email' => "loleves@gmail.com",
            'password' => "password",
            'password_confirmation' => "password"
        ];

        $this->sendRequest($data);

        Event::assertDispatched(UserSignedUp::class);
    }

    /**
     * @test
     * @group apiPost
     */
    public function it_sends_an_email_notification_upon_successful_registration()
    {
        $this->artisan('passport:install');

        Notification::fake();

        $data = [
            'first_name' => "Istvan",
            'last_name' => "Lovas",
            'email' => "loleves@gmail.com",
            'password' => "password",
            'password_confirmation' => "password"
        ];

        $this->sendRequest($data)->assertOk();

        $user = User::firstWhere('email', $data['email']);

        Notification::assertSentTo($user, VerifyEmail::class);

        Notification::assertTimesSent(1, VerifyEmail::class);
    }

    /**
     * @test
     * @group apiPost
     */
    public function it_creates_an_access_token()
    {
        $this->artisan('passport:install');

        Notification::fake();

        $data = [
            'first_name' => "Istvan",
            'last_name' => "Lovas",
            'email' => "loleves@gmail.com",
            'password' => "password",
            'password_confirmation' => "password"
        ];

        $this->sendRequest($data);

        $user = User::firstWhere('email', $data['email']);

        $this->assertDatabaseHas('oauth_access_tokens', ['user_id' => $user->id]);
    }

    /**
     * @test
     * @group apiPost
     */
    public function it_returns_the_user_object()
    {
        $this->artisan('passport:install');

        Notification::fake();

        $data = [
            'first_name' => "Istvan",
            'last_name' => "Lovas",
            'email' => "loleves@gmail.com",
            'password' => "password",
            'password_confirmation' => "password"
        ];

        $this->sendRequest($data)->assertJsonStructure([
            'data' => [
                'message',
                'auth' => [
                    'token',
                    'token_type',
                    'user' => [
                        'id',
                        'name',
                        'first_name',
                        'last_name',
                        'email',
                        'phone',
                        'avatar',
                        'is_verified'
                    ]
                ]
            ]
        ]);
    }

    protected function sendRequest($data = []) : \Illuminate\Testing\TestResponse
    {
        return $this->postJson(route('api.auth.register'), $data);
    }
}
