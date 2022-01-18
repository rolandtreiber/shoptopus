<?php

namespace Tests\PublicApi\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VerifyEmailTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    public function setUp() : void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    /**
     * @test
     * @group apiGet
     */
    public function the_users_email_is_marked_verified_upon_successful_verification()
    {
        $this->assertNull($this->user->email_verified_at);

        $this->sendRequest();

        $this->assertNotNull($this->user->fresh()->email_verified_at);
    }

    protected function sendRequest()
    {
        $this->getJson(route('verification.verify', ['id' => $this->user->id]));
    }
}
