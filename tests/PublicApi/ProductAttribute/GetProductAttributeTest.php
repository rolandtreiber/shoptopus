<?php

namespace Tests\PublicApi\ProductAttribute;

use Tests\TestCase;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeOption;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repositories\Local\ProductAttribute\ProductAttributeRepository;

class GetProductAttributeTest extends TestCase
{
    use RefreshDatabase;

    protected $product;
    protected $product_attribute;

    public function setUp() : void
    {
        parent::setUp();

        $this->product = Product::factory()->create();
        $this->product_attribute = ProductAttribute::factory()->create();
        $option = ProductAttributeOption::factory()->create(['product_attribute_id' => $this->product_attribute->id]);
        $this->product_attribute->products()->attach($this->product->id, ['product_attribute_option_id' => $option->id]);
    }

    /**
     * @test
     * @group apiGet
     */
    public function it_can_return_a_product_attribute_by_its_id()
    {
        $this->sendRequest()
            ->assertOk()
            ->assertSee($this->product_attribute->name);
    }

    /**
     * @test
     * @group apiGet
     */
    public function an_attribute_without_options_are_excluded()
    {
        $this->product_attribute->products()->updateExistingPivot($this->product->id, ['product_attribute_option_id' => null]);

        $this->assertEmpty($this->sendRequest()->json('data'));
    }

    /**
     * @test
     * @group apiGet
     */
    public function an_attribute_without_products_are_excluded()
    {
        $this->product_attribute->products()->updateExistingPivot($this->product->id, ['product_id' => null]);

        $this->assertEmpty($this->sendRequest()->json('data'));
    }

    /**
     * @test
     * @group apiGet
     */
    public function it_returns_all_required_fields()
    {
        $this->sendRequest()
            ->assertJsonStructure([
                'data' => [
                    app()->make(ProductAttributeRepository::class)->getSelectableColumns(false)
                ]
            ]);
    }

    /**
     * @test
     * @group apiGet
     */
    public function it_returns_the_associated_options()
    {
        $res = $this->sendRequest();

        $res->assertJsonStructure([
            'data' => [
                [
                    'options' => [
                        [
                            'id',
                            'name',
                            'slug',
                            'value',
                            'image'
                        ]
                    ]
                ]
            ]
        ]);

        $this->assertCount(1, $res->json('data.0.options'));

        $this->product_attribute->options()->first()->update(['enabled' => false]);

        $this->assertEmpty($this->sendRequest()->json('data'));

        $this->product_attribute->options()->first()->update(['enabled' => true, 'deleted_at' => now()]);

        $this->assertEmpty($this->sendRequest()->json('data'));
    }

    /**
     * @test
     * @group apiGet
     */
    public function it_returns_the_associated_product_ids()
    {
        $res = $this->sendRequest();

        $res->assertJsonStructure([
            'data' => [
                [
                    'product_ids'
                ]
            ]
        ]);

        $this->assertCount(1, $res->json('data.0.product_ids'));

        $this->product->update(['deleted_at' => now()]);

        $this->assertEmpty($this->sendRequest()->json('data'));
    }

    protected function sendRequest() : \Illuminate\Testing\TestResponse
    {
        return $this->getJson(route('api.product_attribute.get', ['id' => $this->product_attribute->id]));
    }
}
