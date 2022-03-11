<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    public function setUp() : void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_has_a_first_name_field()
    {
        $this->assertNotNull($this->user->first_name);
    }

    /** @test */
    public function it_has_a_last_name_field()
    {
        $this->assertNotNull($this->user->last_name);
    }

    /** @test */
    public function it_has_a_name_field()
    {
        $this->assertEquals(
            trim($this->user->prefix . ' ' . $this->user->first_name . ' ' . $this->user->last_name),
            $this->user->name
        );
    }

    /** @test */
    public function it_has_an_email_field()
    {
        $this->assertNotNull($this->user->email);
    }

    /** @test */
    public function it_has_a_password_field()
    {
        $this->assertNotNull($this->user->password);
    }

    /** @test */
    public function it_has_a_prefix_field()
    {
        $this->assertNull($this->user->prefix);
    }

    /** @test */
    public function it_has_an_initials_field()
    {
        $this->assertEquals(
            $this->user->initials,
            substr($this->user->first_name, 0, 1) . substr($this->user->last_name, 0, 1)
        );
    }

    /** @test */
    public function it_has_a_phone_field()
    {
        $this->assertNull($this->user->phone);
    }

    /** @test */
    public function it_has_a_temporary_field()
    {
        $this->assertFalse($this->user->temporary);
    }

    /** @test */
    public function it_has_an_is_favorite_field()
    {
        $this->assertFalse($this->user->is_favorite);
    }

    /** @test */
    public function it_has_an_avatar_field()
    {
        $this->assertNotNull($this->user->avatar->url);
        $this->assertNotNull($this->user->avatar->file_name);
    }

    /** @test */
    public function it_has_an_email_verified_at_field()
    {
        $this->assertNull($this->user->email_verified_at);
    }

    /** @test */
    public function it_may_have_many_addresses()
    {
        $this->assertCount(0, $this->user->addresses);

        Address::factory()->count(2)->create(['user_id' => $this->user->id]);

        $this->assertCount(2, $this->user->refresh()->addresses);

        $this->assertInstanceOf(Address::class, $this->user->addresses[0]);
    }
}
