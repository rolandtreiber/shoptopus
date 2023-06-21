<?php

namespace Tests\PublicApi\Product;

use App\Events\UserInteraction;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class FavoriteProductTest extends TestCase
{
    use RefreshDatabase;

    public $user;

    public $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->product = Product::factory()->create();
    }

    /**
     * @test
     *
     * @group apiPost
     */
    public function unauthenticated_users_are_not_allowed_to_favorite_products(): void
    {
        $res = $this->sendRequest()->json();

        $this->assertEquals('Unauthenticated.', $res['developer_message']);
        $this->assertEquals('Sorry there was a system error, the administrator has been informed.', $res['user_message']);
    }

    /**
     * @test
     *
     * @group apiPost
     */
    public function it_requires_a_valid_product_id(): void
    {
        $this->signIn($this->user)
            ->postJson(route('api.product.favorite', ['id' => '12345']))
            ->assertJsonValidationErrors(['productId']);
    }

    /**
     * @test
     *
     * @group apiPost
     */
    public function authorised_users_can_favorite_products(): void
    {
        $this->assertDatabaseMissing('favorited_products', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
        ]);

        $this->signIn($this->user)
            ->sendRequest()
            ->assertOk();

        $this->assertDatabaseHas('favorited_products', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
        ]);
    }

    /**
     * @test
     *
     * @group apiPost
     */
    public function a_product_can_be_unfavorited(): void
    {
        $this->signIn($this->user);

        $this->sendRequest();

        $this->assertDatabaseHas('favorited_products', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
        ]);

        $this->sendRequest();

        $this->assertDatabaseMissing('favorited_products', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
        ]);
    }

    /**
     * @test
     *
     * @group apiPost
     */
    public function favouriting_product_updates_last_seen(): void {
        $this->assertDatabaseMissing('favorited_products', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
        ]);

        $this->user->last_seen = null;
        $this->user->save();

        $this->signIn($this->user)
            ->sendRequest()
            ->assertOk();
        $this->user->refresh();
        $this->assertTrue($this->user->last_seen->timestamp <= Carbon::now()->timestamp);
    }

    /**
     * @test
     *
     * @group apiPost
     */
    public function favouriting_product_triggers_user_interaction_event(): void {
        Event::fake();
        $this->assertDatabaseMissing('favorited_products', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
        ]);

        $this->signIn($this->user)
            ->sendRequest()
            ->assertOk();
        $this->user->refresh();
        Event::assertDispatched(UserInteraction::class);
    }

    /**
     * @test
     *
     * @group apiPost
     */
    public function unfavouriting_product_updates_last_seen(): void {
        $this->signIn($this->user);
        $this->sendRequest();
        $this->assertDatabaseHas('favorited_products', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
        ]);

        $this->sendRequest();
        $this->user->refresh();
        $this->assertTrue($this->user->last_seen->timestamp <= Carbon::now()->timestamp);
    }

    /**
     * @test
     *
     * @group apiPost
     */
    public function unfavouriting_product_triggers_user_interaction_event(): void {
        Event::fake();
        $this->signIn($this->user);
        $this->sendRequest();
        $this->assertDatabaseHas('favorited_products', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
        ]);
        $this->user->last_seen = null;
        $this->user->save();
        $this->sendRequest();
        $this->user->refresh();
        Event::assertDispatched(UserInteraction::class);
    }

    protected function sendRequest(): \Illuminate\Testing\TestResponse
    {
        return $this->postJson(route('api.product.favorite', ['id' => $this->product->id]));
    }
}
