<?php

namespace Tests\PublicApi\ProductTag;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductTag;
use App\Repositories\Local\ProductTag\ProductTagRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

/**
 * @group product_tags
 * @group product_tags_storefront
 */
class GetAllProductTagsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     *
     * @group apiGetAll
     */
    public function it_returns_the_correct_format(): void
    {
        $this->sendRequest()
            ->assertJsonStructure([
                'message',
                'data',
                'next',
                'records',
                'total_records',
            ]);
    }

    /**
     * @test
     *
     * @group apiGetAll
     */
    public function it_returns_all_required_fields(): void
    {
        ProductTag::factory()->count(2)->create();

        $res = $this->sendRequest();

        $res->assertJsonStructure([
            'data' => [
                app()->make(ProductTagRepository::class)->getSelectableColumns(false),
            ],
        ]);

        $this->assertCount(2, $res->json('data'));
    }

    /**
     * @test
     *
     * @group apiGetAll
     */
    public function soft_deleted_and_disabled_product_tags_are_not_returned(): void
    {
        ProductTag::factory()->count(2)->create(['deleted_at' => now()]);
        ProductTag::factory()->create(['enabled' => false]);

        $this->assertEmpty($this->sendRequest()->json('data'));
    }

    /**
     * @test
     *
     * @group apiGetAll
     */
    public function it_returns_the_count(): void
    {
        ProductTag::factory()->count(2)->create();

        $this->assertEquals(2, $this->sendRequest()->json('total_records'));
    }

    /**
     * @test
     *
     * @group apiGetAll
     */
    public function product_tags_can_be_filtered_by_id(): void
    {
        ProductTag::factory()->count(3)->create();
        $product_tag = ProductTag::factory()->create();

        $res = $this->sendRequest(['filter[id]' => $product_tag->id]);

        $this->assertCount(1, $res->json('data'));
        $this->assertEquals($product_tag->id, $res->json('data.0.id'));
    }

    /**
     * @test
     *
     * @group apiGetAll
     */
    public function filters_can_accept_multiple_parameters(): void
    {
        ProductTag::factory()->count(3)->create();
        $product_tag1 = ProductTag::factory()->create();
        $product_tag2 = ProductTag::factory()->create();

        $res = $this->sendRequest(['filter[id]' => implode(',', [$product_tag1->id, $product_tag2->id])]);

        $this->assertCount(2, $res->json('data'));
        $this->assertEquals($product_tag1->id, $res->json('data.0.id'));
        $this->assertEquals($product_tag2->id, $res->json('data.1.id'));
    }

    /**
     * @test
     *
     * @group apiGetAll
     */
    public function it_returns_product_count_correctly()
    {
        $productTags = ProductTag::factory()->count(3)->create();
        $products = Product::factory()->count(3)->create();
        $products[0]->product_tags()->attach([$productTags[0]->id]);
        $products[1]->product_tags()->attach([$productTags[0]->id, $productTags[1]->id]);
        $products[2]->product_tags()->attach([$productTags[0]->id, $productTags[2]->id]);

        $res = $this->sendRequest(['filter[id]' => $productTags[0]->id]);
        $this->assertEquals(3, $res->json('data.0.products_count'));
        $res = $this->sendRequest(['filter[id]' => $productTags[1]->id]);
        $this->assertEquals(1, $res->json('data.0.products_count'));
        $res = $this->sendRequest(['filter[id]' => $productTags[2]->id]);
        $this->assertEquals(1, $res->json('data.0.products_count'));
    }

    protected function sendRequest($data = []): TestResponse
    {
        return $this->getJson(route('api.product_tags.getAll', $data));
    }

}
