<?php

namespace Tests\PublicApi\User;

use App\Models\Product;
use App\Models\User;
use App\Repositories\Local\Product\ProductRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetFavoritesTest extends TestCase
{
    use RefreshDatabase;

    public $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    /**
     * @test
     * @group apiGet
     */
    public function unauthenticated_users_are_not_allowed_to_get_favorite_products()
    {
        $res = $this->sendRequest()->json();

        $this->assertEquals('Unauthenticated.', $res['developer_message']);
        $this->assertEquals('Sorry there was a system error, the administrator has been informed.', $res['user_message']);
    }

    /**
     * @test
     * @group apiGet
     */
    public function authenticated_users_can_get_their_favorited_products()
    {
        $products = Product::factory()->count(2)->create();

        $this->signIn($this->user);

        $this->assertEmpty($this->sendRequest()->json('data'));

        $products->each(fn ($product) => $this->postJson(route('api.product.favorite', ['id' => $product->id])));

        $res = $this->sendRequest();

        $res->assertJsonStructure([
            'data' => [
                app()->make(ProductRepository::class)->getSelectableColumns(false),
            ],
        ]);

        $this->assertCount($products->count(), $res->json('data'));
    }

    protected function sendRequest(): \Illuminate\Testing\TestResponse
    {
        return $this->getJson(route('api.user.favorites'));
    }
}
