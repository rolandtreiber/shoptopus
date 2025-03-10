<?php

namespace Tests\PublicApi\Auth;

use App\Events\UserInteraction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * @group login
 */
class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
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
            'password' => null,
        ];

        $this->sendRequest($data)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    /**
     * @test
     *
     * @group apiPost
     */
    public function it_requires_a_valid_email(): void
    {
        $data = [
            'email' => 'lolevesgmail',
            'password' => 'password',
        ];

        $this->sendRequest($data)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * @test
     *
     * @group apiPost
     */
    public function it_requires_a_correct_password(): void
    {
        $data = [
            'email' => $this->user->email,
            'password' => 'passwordssssss',
        ];

        $res = $this->sendRequest($data)->json();

        $this->assertEquals('Incorrect email or password. Please try again.', $res['user_message']);
        $this->assertEquals('Hash check fail', $res['developer_message']);
    }

    /**
     * @test
     *
     * @group apiPost
     */
    public function it_returns_the_correct_error_message_when_no_user_is_found(): void
    {
        $data = [
            'email' => 'lolevesP@gmai.com',
            'password' => 'password',
        ];

        $res = $this->sendRequest($data)->json();

        $this->assertEquals('Incorrect email, password or not verified please try again.', $res['user_message']);
        $this->assertEquals('User not found.', $res['developer_message']);
    }

    /**
     * @test
     *
     * @group apiPost
     */
    public function it_returns_the_authenticated_user_with_a_token_upon_successful_login(): void
    {
        $this->artisan('passport:install');

        $data = [
            'email' => $this->user->email,
            'password' => 'password',
        ];

        $this->sendRequest($data)->assertJsonStructure([
            'data' => [
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
                        'avatar' => [
                            'url',
                            'file_name',
                        ],
                        'is_verified',
                        'cart' => [
                            'id',
                            'user_id',
                            'ip_address',
                            'user',
                            'products',
                        ],
                        'notifications',
                        'favorites',
                    ],
                ],
            ],
        ]);

        $this->assertDatabaseHas('oauth_access_tokens', ['user_id' => $this->user->id]);
    }

    /**
     * @test
     */
    public function it_updates_last_seen(): void
    {
        $this->artisan('passport:install');
        $this->user->last_seen = null;
        $this->user->save();

        $data = [
            'email' => $this->user->email,
            'password' => 'password',
        ];

        $this->sendRequest($data);
        $this->user->refresh();
        $this->assertTrue($this->user->last_seen->timestamp <= Carbon::now()->timestamp);
    }

    /**
     * @test
     */
    public function it_triggers_user_interaction_event(): void
    {
        Event::fake();
        $this->artisan('passport:install');

        $data = [
            'email' => $this->user->email,
            'password' => 'password',
        ];

        $this->sendRequest($data);

        $this->user->last_seen = null;
        $this->user->save();
        Event::assertDispatched(UserInteraction::class);
    }

    /**
     * @test
     *
     * @group apiPost
     */
    public function the_notifications_array_contains_all_the_unread_notifications(): void
    {
        $this->artisan('passport:install');

        $notificationData = [
            'type' => 'competition-entered',
            'title' => 'You have entered a Competition.',
            'level' => 'info',
        ];

        DB::table('notifications')->insert([
            'id' => Str::uuid(),
            'type' => 'App\Notifications\YouEnteredACompetition',
            'notifiable_type' => 'App\Models\User\User',
            'notifiable_id' => $this->user->id,
            'data' => json_encode($notificationData),
        ]);

        DB::table('notifications')->insert([
            'id' => Str::uuid(),
            'type' => 'App\Notifications\YouEnteredACompetition',
            'notifiable_type' => 'App\Models\User\User',
            'notifiable_id' => $this->user->id,
            'data' => json_encode($notificationData),
            'read_at' => now(),
        ]);

        $data = [
            'email' => $this->user->email,
            'password' => 'password',
        ];

        $notifications = $this->sendRequest($data)->json('data.auth.user.notifications');

        $this->assertCount(1, $notifications);
    }

    /**
     * @test
     *
     * @group apiPost
     */
    public function users_without_a_password_are_sent_the_correct_error_response(): void
    {
        $this->user->update(['password' => null]);

        $data = [
            'email' => $this->user->email,
            'password' => 'randompassword',
        ];

        $res = $this->sendRequest($data)->json();

        $this->assertEquals('Please reset your password.', $res['user_message']);
    }

    protected function sendRequest($data = []): \Illuminate\Testing\TestResponse
    {
        return $this->postJson(route('api.auth.login'), $data);
    }
}
