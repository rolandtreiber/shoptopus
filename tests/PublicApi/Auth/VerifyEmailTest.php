<?php

namespace Tests\PublicApi\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class VerifyEmailTest extends TestCase
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
     * @group apiGet
     */
    public function it_requires_a_valid_url(): void
    {
        $this->assertNull($this->user->email_verified_at);

        $res = $this->sendRequest()->json('url');

        $parsed_url = parse_url($res);

        $this->assertEquals('/verify', $parsed_url['path']);

        $this->assertEquals('message=Invalid/Expired url provided.&status=401', urldecode($parsed_url['query']));

        $this->assertNull($this->user->fresh()->email_verified_at);
    }

    /**
     * @test
     *
     * @group apiGet
     */
    public function the_users_email_is_marked_verified_upon_successful_verification(): void
    {
        $url = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addYear(),
            [
                'id' => $this->user->getKey(),
                'hash' => sha1($this->user->getEmailForVerification()),
            ]
        );

        $query_string = '?'.parse_url($url)['query'];

        $this->assertNull($this->user->email_verified_at);

        $this->sendRequest($query_string)->assertJsonStructure(['url']);

        $this->assertNotNull($this->user->fresh()->email_verified_at);
    }

    protected function sendRequest(string $query_string = ''): \Illuminate\Testing\TestResponse
    {
        return $this->getJson(route('verification.verify', ['id' => $this->user->id]).$query_string);
    }
}
