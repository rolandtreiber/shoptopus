<?php

namespace Tests\PublicApi\User;

use App\Mail\UserAccountSuccessfullyDeactivatedEmail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * @group user-account-deactivation
 */
class UserAccountDeactivationTest extends TestCase
{
    use RefreshDatabase;

    public $user;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        $this->user = User::factory()->create();
    }

    public function test_user_can_deactivate_own_account_without_anonimizing(): void
    {
        $this->signIn($this->user);
        $originalEmail = $this->user->email;
        $response = $this->delete(route('api.user.delete.account')."?anonimize=0")->json();
        $this->assertEquals($response["message"], "User account deleted");
        $this->assertDatabaseMissing('users', [
           'email' => $originalEmail
        ]);
        /** @var User $userAfterState */
        $userAfterState = User::withTrashed()->first();
        $this->assertStringContainsString("DEACTIVATED-", $userAfterState->email);
        Mail::assertSent(UserAccountSuccessfullyDeactivatedEmail::class);
    }

    public function test_user_can_deactivate_own_account_with_anonimizing(): void
    {
        $this->signIn($this->user);
        $originalEmail = $this->user->email;
        $response = $this->delete(route('api.user.delete.account')."?anonimize=1")->json();
        $this->assertEquals($response["message"], "User account deleted");
        $this->assertDatabaseMissing('users', [
            'email' => $originalEmail
        ]);
        /** @var User $userAfterState */
        $userAfterState = User::withTrashed()->first();
        $this->assertStringNotContainsString("DEACTIVATED-", $userAfterState->email);
        $this->assertStringNotContainsString($originalEmail, $userAfterState->email);
        Mail::assertSent(UserAccountSuccessfullyDeactivatedEmail::class);
    }

}
