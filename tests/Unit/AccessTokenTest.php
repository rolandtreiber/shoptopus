<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\AccessToken;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AccessTokenTest extends TestCase
{
    use RefreshDatabase;

    protected $access_token;

    public function setUp() : void
    {
        parent::setUp();

        $this->access_token = AccessToken::factory()->create();
    }

    /** @test */
    public function it_has_a_type_field()
    {
        $this->assertNotNull($this->access_token->type);
    }

    /** @test */
    public function it_has_a_token_field()
    {
        $this->assertNotNull($this->access_token->token);
    }

    /** @test */
    public function it_belongs_to_a_user()
    {
        $this->assertNotNull($this->access_token->user);

        $this->assertInstanceOf(User::class, $this->access_token->fresh()->user);
    }

    /** @test */
    public function it_belongs_to_an_issuer()
    {
        $this->assertNotNull($this->access_token->issuer);

        $this->assertInstanceOf(User::class, $this->access_token->fresh()->issuer);
    }
}
