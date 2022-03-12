<?php

namespace Tests\PublicApi\Cart;

use Tests\TestCase;
use App\Models\Cart;
use App\Models\User;
use App\Models\Product;
use App\Services\Local\Error\ErrorService;
use App\Repositories\Local\Cart\CartRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetCartTest extends TestCase
{
    use RefreshDatabase;

    protected $cart;

    public function setUp() : void
    {
        parent::setUp();

        $this->cart = Cart::factory()->create();
    }

    /**
     * @test
     * @group apiGet
     */
    public function unauthenticated_users_are_not_allowed_to_get_the_cart()
    {
        $unAuthenticatedRes = $this->sendRequest()->json();

        $this->assertEquals('Unauthenticated.', $unAuthenticatedRes['developer_message']);
        $this->assertEquals('Sorry there was a system error, the administrator has been informed.', $unAuthenticatedRes['user_message']);
    }

    /**
     * @test
     * @group apiGet
     */
    public function it_returns_the_cart_by_its_id()
    {
        $this->signIn()->sendRequest()->assertOk();
    }

    /**
     * @test
     * @group apiGet
     */
    public function it_returns_all_required_fields()
    {
        $this->signIn()
            ->sendRequest()
            ->assertJsonStructure([
                'data' => [
                    $this->getModelRepo()->getSelectableColumns(false)
                ]
            ]);
    }

    /**
     * @test
     * @group apiGet
     */
    public function it_returns_the_associated_user()
    {
        $user = User::factory()->create();
        $this->cart->update(['user_id' => $user->id]);

        $this->signIn();

        $res = $this->sendRequest();

        $res->assertJsonStructure([
            'data' => [
                [
                    'user' => [
                        'id',
                        'first_name',
                        'last_name',
                        'email',
                        'name',
                        'initials',
                        'prefix',
                        'phone',
                        'avatar',
                        'email_verified_at',
                        'client_ref',
                        'temporary',
                        'is_favorite'
                    ]
                ]
            ]
        ]);

        $user->update(['deleted_at' => now()]);

        $this->assertNull($this->sendRequest()->json('data.0.user'));
    }

    /**
     * @test
     * @group apiGet
     */
    public function it_returns_the_associated_products_even_if_it_is_deleted()
    {
        $product = Product::factory()->create(['deleted_at' => now()]);
        $this->cart->products()->attach($product);

        $this->signIn();

        $res = $this->sendRequest();

        $res->assertJsonStructure([
            'data' => [
                [
                    'products' => [
                        [
                            'product_variant_id',
                            'quantity',
                            'id',
                            'name',
                            'short_description',
                            'description',
                            'price',
                            'status',
                            'purchase_count',
                            'stock',
                            'backup_stock',
                            'sku',
                            'cover_photo',
                            'rating'
                        ]
                    ]
                ]
            ]
        ]);
    }

    protected function getModelRepo() : CartRepository
    {
        $errorService = new ErrorService;
        return new CartRepository($errorService, new Cart);
    }

    protected function sendRequest() : \Illuminate\Testing\TestResponse
    {
        return $this->getJson(route('api.cart.get', ['id' => $this->cart->id]));
    }
}
