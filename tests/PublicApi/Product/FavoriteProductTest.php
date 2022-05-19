<?php

namespace Tests\PublicApi\Product;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FavoriteProductTest extends TestCase
{
    use RefreshDatabase;

    public $user;
    public $product;

    public function setUp() : void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->product = Product::factory()->create();
    }

    /**
     * @test
     * @group apiPost
     */
    public function unauthenticated_users_are_not_allowed_to_favorite_products()
    {
        $res = $this->sendRequest()->json();

        $this->assertEquals('Unauthenticated.', $res['developer_message']);
        $this->assertEquals('Sorry there was a system error, the administrator has been informed.', $res['user_message']);
    }

    /**
     * @test
     * @group apiPost
     */
    public function it_requires_a_valid_product_id()
    {
        $this->signIn($this->user)
            ->postJson(route('api.products.favorite', ['id' => '12345']))
            ->assertJsonValidationErrors(['productId']);
    }

    /**
     * @test
     * @group apiPost
     */
    public function authorised_users_can_favorite_products()
    {
        $this->assertDatabaseMissing('favorited_products', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id
        ]);

        $this->signIn($this->user)
            ->sendRequest()
            ->assertOk();

        $this->assertDatabaseHas('favorited_products', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id
        ]);
    }

    /**
     * @test
     * @group apiPost
     */
    public function a_product_can_be_unfavorited()
    {
        $this->signIn($this->user);

        $this->sendRequest();

        $this->assertDatabaseHas('favorited_products', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id
        ]);

        $this->sendRequest();

        $this->assertDatabaseMissing('favorited_products', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id
        ]);
    }

    protected function sendRequest() : \Illuminate\Testing\TestResponse
    {
        return $this->postJson(route('api.products.favorite', ['id' => $this->product->id]));
    }
}
