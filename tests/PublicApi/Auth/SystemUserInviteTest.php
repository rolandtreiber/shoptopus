<?php

namespace Tests\PublicApi\Auth;


use App\Enums\AccessTokenType;
use App\Mail\SystemUserInviteEmail;
use App\Models\AccessToken;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\TestResponse;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

/**
 * @group system_user_invite
 */
class SystemUserInviteTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    const ROUTES = [
        'INVITE_USER' => 'admin.api.invite.user',
        'REGISTER_BY_INVITE' => 'api.auth.register-by-invite'
    ];

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        Notification::fake();
        $this->user = User::factory()->create();
        $this->app->make(PermissionRegistrar::class)->forgetCachedPermissions();
        self::flushRolesAndPermissions();
        $this->user->assignRole('super_admin');
    }

    /**
     * @test
     */
    public function can_invite_system_user_when_user_has_permission()
    {
        $res = $this->signIn($this->user)->sendRequest([
            'email' => 'test@email.com',
            'role' => 'store_manager'
        ], self::ROUTES['INVITE_USER']);
        $res->assertOk();
        Mail::assertSent(SystemUserInviteEmail::class);
        $this->assertDatabaseHas('access_tokens', [
            'type' => AccessTokenType::SignupRequest
        ]);
    }

    /**
     * @test
     */
    public function cannot_invite_system_user_when_does_not_have_permission()
    {
        $this->user->removeRole(Role::findByName('super_admin'));
        $res = $this->signIn($this->user)->sendRequest([
            'email' => 'test@email.com',
            'role' => 'store_manager'
        ], self::ROUTES['INVITE_USER']);
        $res->assertForbidden();
        Mail::assertNothingSent();
        Mail::assertNothingQueued();
        $this->assertDatabaseEmpty('access_tokens');
    }

    /**
     * @test
     */
    public function invite_validation_works_on_duplicate_email()
    {
        $preSeededUser = User::factory()->create();
        $res = $this->signIn($this->user)->sendRequest([
            'email' => $preSeededUser->email,
            'role' => 'store_manager'
        ], self::ROUTES['INVITE_USER']);
        $res->assertStatus(422);
        Mail::assertNothingSent();
        Mail::assertNothingQueued();
        $this->assertDatabaseEmpty('access_tokens');
    }

    /**
     * @test
     */
    public function user_can_register_by_invitation()
    {
        $this->artisan('passport:install');
        $accessToken = new AccessToken();
        $accessToken->type = AccessTokenType::SignupRequest;
        $accessToken->content = json_encode([
            "role" => "store_assistant",
            "email" => "testemail1@gmail.com"
        ]);
        $accessToken->accessable_type = User::class;
        $accessToken->accessable_id = "NEW USER";
        $accessToken->user_id = $this->user->id;
        $accessToken->issuer_user_id = $this->user->id;
        $accessToken->expiry = Carbon::now()->addDay();
        $accessToken->save();

        $res = $this->sendRequest([
            'first_name' => 'John',
            'last_name' => 'Smith',
            'password' => 'MyStrongPassword01$',
            'password_confirmation' => 'MyStrongPassword01$'
        ], self::ROUTES['REGISTER_BY_INVITE'], [
            'token' => $accessToken->token
        ]);
        $res->assertOk();
        $this->assertDatabaseHas('users', [
            "email" => "testemail1@gmail.com"
        ]);
    }

    /**
     * @test
     */
    public function register_by_invite_fails_if_access_token_is_expired()
    {
        $this->artisan('passport:install');
        $accessToken = new AccessToken();
        $accessToken->type = AccessTokenType::SignupRequest;
        $accessToken->content = json_encode([
            "role" => "store_assistant",
            "email" => "testemail1@gmail.com"
        ]);
        $accessToken->accessable_type = User::class;
        $accessToken->accessable_id = "NEW USER";
        $accessToken->user_id = $this->user->id;
        $accessToken->issuer_user_id = $this->user->id;
        $accessToken->expiry = Carbon::now()->subMinute();
        $accessToken->save();

        $res = $this->sendRequest([
            'first_name' => 'John',
            'last_name' => 'Smith',
            'password' => 'MyStrongPassword01$',
            'password_confirmation' => 'MyStrongPassword01$'
        ], self::ROUTES['REGISTER_BY_INVITE'], [
            'token' => $accessToken->token
        ]);
        $res->assertJsonFragment([
            "developer_message" => "Invalid or expired token."
        ]);
        $this->assertDatabaseMissing('users', [
            "email" => "testemail1@gmail.com"
        ]);
    }

    /**
     * @test
     */
    public function register_by_invite_fails_if_access_token_is_of_wrong_type()
    {
        $this->artisan('passport:install');
        $accessToken = new AccessToken();
        $accessToken->type = AccessTokenType::Invoice;
        $accessToken->content = json_encode([
            "role" => "store_assistant",
            "email" => "testemail1@gmail.com"
        ]);
        $accessToken->accessable_type = User::class;
        $accessToken->accessable_id = "NEW USER";
        $accessToken->user_id = $this->user->id;
        $accessToken->issuer_user_id = $this->user->id;
        $accessToken->expiry = Carbon::now()->addDay();
        $accessToken->save();

        $res = $this->sendRequest([
            'first_name' => 'John',
            'last_name' => 'Smith',
            'password' => 'MyStrongPassword01$',
            'password_confirmation' => 'MyStrongPassword01$'
        ], self::ROUTES['REGISTER_BY_INVITE'], [
            'token' => $accessToken->token
        ]);
        $res->assertJsonFragment([
            "developer_message" => "Invalid or expired token."
        ]);
        $this->assertDatabaseMissing('users', [
            "email" => "testemail1@gmail.com"
        ]);
    }

    /**
     * @test
     */
    public function register_by_invite_new_user_has_correct_role()
    {
        $this->artisan('passport:install');
        $accessToken = new AccessToken();
        $accessToken->type = AccessTokenType::SignupRequest;
        $accessToken->content = json_encode([
            "role" => "store_assistant",
            "email" => "testemail1@gmail.com"
        ]);
        $accessToken->accessable_type = User::class;
        $accessToken->accessable_id = "NEW USER";
        $accessToken->user_id = $this->user->id;
        $accessToken->issuer_user_id = $this->user->id;
        $accessToken->expiry = Carbon::now()->addDay();
        $accessToken->save();

        $res = $this->sendRequest([
            'first_name' => 'John',
            'last_name' => 'Smith',
            'password' => 'MyStrongPassword01$',
            'password_confirmation' => 'MyStrongPassword01$'
        ], self::ROUTES['REGISTER_BY_INVITE'], [
            'token' => $accessToken->token
        ]);
        $res->assertOk();
        /** @var User $user */
        $user = User::where('email', "testemail1@gmail.com")->first();
        self::assertTrue($user->hasRole(['store_assistant']));
    }

    protected function sendRequest($data = [], $route = self::ROUTES["INVITE_USER"], $routeParams = []): TestResponse
    {
        return $this->postJson(route($route, $routeParams), $data);
    }

}
