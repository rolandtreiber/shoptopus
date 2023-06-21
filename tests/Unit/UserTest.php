<?php

namespace Tests\Unit;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentSource;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_has_a_first_name_field(): void
    {
        $this->assertNotNull($this->user->first_name);
    }

    /** @test */
    public function it_has_a_last_name_field(): void
    {
        $this->assertNotNull($this->user->last_name);
    }

    /** @test */
    public function it_has_a_slug_generated_from_its_first_and_last_name(): void
    {
        $this->assertEquals(Str::slug($this->user->first_name.' '.$this->user->last_name), $this->user->slug);
    }

    /** @test */
    public function it_has_a_name_field(): void
    {
        $this->assertEquals(
            trim($this->user->prefix.' '.$this->user->first_name.' '.$this->user->last_name),
            $this->user->name
        );
    }

    /** @test */
    public function it_has_an_email_field(): void
    {
        $this->assertNotNull($this->user->email);
    }

    /** @test */
    public function it_has_a_password_field(): void
    {
        $this->assertNotNull($this->user->password);
    }

    /** @test */
    public function it_has_a_prefix_field(): void
    {
        $this->assertNull($this->user->prefix);
    }

    /** @test */
    public function it_has_an_initials_field(): void
    {
        $this->assertEquals(
            $this->user->initials,
            substr($this->user->first_name, 0, 1).substr($this->user->last_name, 0, 1)
        );
    }

    /** @test */
    public function it_has_a_phone_field(): void
    {
        $this->assertNull($this->user->phone);
    }

    /** @test */
    public function it_has_a_temporary_field(): void
    {
        $this->assertFalse($this->user->temporary);
    }

    /** @test */
    public function it_has_an_is_favorite_field(): void
    {
        $this->assertFalse($this->user->is_favorite);
    }

    /** @test */
    public function it_has_an_avatar_field(): void
    {
        $this->assertNotNull($this->user->avatar->url);
        $this->assertNotNull($this->user->avatar->file_name);
    }

    /** @test */
    public function it_has_an_email_verified_at_field(): void
    {
        $this->assertNull($this->user->email_verified_at);
    }

    /** @test */
    public function it_has_a_last_seen_field(): void
    {
        $this->assertNotNull($this->user->last_seen);
    }

    /** @test */
    public function it_may_have_many_addresses(): void
    {
        $this->assertCount(0, $this->user->addresses);

        Address::factory()->count(2)->create(['user_id' => $this->user->id]);

        $this->assertCount(2, $this->user->refresh()->addresses);

        $this->assertInstanceOf(Address::class, $this->user->addresses[0]);
    }

    /** @test */
    public function it_may_have_many_orders(): void
    {
        $this->assertCount(0, $this->user->orders);

        Order::factory()->count(2)->create(['user_id' => $this->user->id]);

        $this->assertCount(2, $this->user->refresh()->orders);

        $this->assertInstanceOf(Order::class, $this->user->orders[0]);
    }

    /** @test */
    public function it_may_have_many_payment_sources(): void
    {
        $this->assertCount(0, $this->user->payment_sources);

        PaymentSource::factory()->count(2)->create(['user_id' => $this->user->id]);

        $this->assertCount(2, $this->user->refresh()->payment_sources);

        $this->assertInstanceOf(PaymentSource::class, $this->user->payment_sources[0]);
    }

    /** @test */
    public function it_may_have_many_payments(): void
    {
        $this->assertCount(0, $this->user->payments);

        Payment::factory()->count(2)->create(['user_id' => $this->user->id]);

        $this->assertCount(2, $this->user->refresh()->payments);

        $this->assertInstanceOf(Payment::class, $this->user->payments[0]);
    }

    /** @test */
    public function a_cart_is_added_for_the_user_on_creation(): void
    {
        $this->assertInstanceOf(Cart::class, $this->user->cart);
    }

    /** @test */
    public function it_may_have_many_social_accounts(): void
    {
        $this->assertCount(0, $this->user->social_accounts);

        SocialAccount::factory()->create(['user_id' => $this->user->id]);

        $this->assertCount(1, $this->user->refresh()->social_accounts);

        $this->assertInstanceOf(SocialAccount::class, $this->user->social_accounts[0]);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->user->social_accounts);
    }
}
