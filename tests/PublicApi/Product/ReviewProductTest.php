<?php

namespace Tests\PublicApi\Product;

use App\Enums\OrderStatus;
use App\Models\AccessToken;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ReviewProductTest extends TestCase
{
    use RefreshDatabase;

    protected Order $order;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->order = Order::factory()->state([
            'status' => OrderStatus::AwaitingPayment
        ])->create();
    }

    /**
     * @test
     */
    public function test_product_review_can_be_saved(): void
    {
        Mail::fake();
        /** @var Product $product */
        $product = Product::factory()->create();
        $this->order->products()->attach($product->id);
        $this->order->status = OrderStatus::Completed;
        $this->order->save();
        $accessToken = AccessToken::first();
        $product = $this->order->products()->first();
        $this->sendRequest([
                'id' => $product,
                'token' => $accessToken->token,
                'title' => 'Test review',
                'description' => 'Test description',
                'rating' => 4,
                'language_prefix' => 'en'
            ])
            ->assertOk();
    }

    /**
     * @test
     */
    public function test_product_review_requires_valid_token(): void
    {
        Mail::fake();
        /** @var Product $product */
        $product = Product::factory()->create();
        $this->order->products()->attach($product->id);
        $this->order->status = OrderStatus::Completed;
        $this->order->save();
        $product = $this->order->products()->first();
        $res = $this->sendRequest([
                'id' => $product,
                'token' => "CLEARLY INVALID TOKEN",
                'title' => 'Test review',
                'description' => 'Test description',
                'rating' => 4,
                'language_prefix' => 'en'
            ]);
        $res->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_product_review_rating_needs_to_be_between_1_and_5(): void
    {
        Mail::fake();
        /** @var Product $product */
        $product = Product::factory()->create();
        $this->order->products()->attach($product->id);
        $this->order->status = OrderStatus::Completed;
        $this->order->save();
        $accessToken = AccessToken::first();
        $product = $this->order->products()->first();
        $res = $this->sendRequest([
            'id' => $product,
            'token' => $accessToken->token,
            'title' => 'Test review',
            'description' => 'Test description',
            'rating' => 6,
            'language_prefix' => 'en'
        ]);
        $res->assertStatus(422);

        $res = $this->sendRequest([
            'id' => $product,
            'token' => $accessToken->token,
            'title' => 'Test review',
            'description' => 'Test description',
            'rating' => 0,
            'language_prefix' => 'en'
        ]);
        $res->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_product_review_rating_has_all_required_fields(): void
    {
        Mail::fake();
        /** @var Product $product */
        $product = Product::factory()->create();
        $this->order->products()->attach($product->id);
        $this->order->status = OrderStatus::Completed;
        $this->order->save();
        $accessToken = AccessToken::first();
        $product = $this->order->products()->first();
        $res = $this->sendRequest([
            'id' => $product,
            'token' => $accessToken->token,
            'description' => 'Test description',
            'rating' => 4,
            'language_prefix' => 'en'
        ]);
        $res->assertStatus(422);

        $res = $this->sendRequest([
            'id' => $product,
            'token' => $accessToken->token,
            'title' => 'Test review',
            'rating' => 4,
            'language_prefix' => 'en'
        ]);
        $res->assertStatus(422);

        $res = $this->sendRequest([
            'id' => $product,
            'token' => $accessToken->token,
            'title' => 'Test review',
            'description' => 'Test description',
            'language_prefix' => 'en'
        ]);
        $res->assertStatus(422);
    }

    protected function sendRequest($payload): \Illuminate\Testing\TestResponse
    {
        return $this->postJson(route('api.product.save.review', $payload));
    }
}
