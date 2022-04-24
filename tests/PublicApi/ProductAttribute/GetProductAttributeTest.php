<?php

namespace Tests\PublicApi\ProductAttribute;

use Tests\TestCase;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeOption;
use App\Services\Local\Error\ErrorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repositories\Local\ProductAttribute\ProductAttributeRepository;

class GetProductAttributeTest extends TestCase
{
    use RefreshDatabase;

    protected $product_attribute;

    public function setUp() : void
    {
        parent::setUp();

        $this->product_attribute = ProductAttribute::factory()->create();
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
    public function it_returns_all_required_fields()
    {
        $this->sendRequest()
            ->assertJsonStructure([
                'data' => [
                    (new ProductAttributeRepository(new ErrorService, new ProductAttribute))->getSelectableColumns(false)
                ]
            ]);
    }

    /**
     * @test
     * @group apiGet
     */
    public function it_returns_the_associated_options()
    {
        $pao = ProductAttributeOption::factory()->create();
        $pao2 = ProductAttributeOption::factory()->create();
        $this->product_attribute->options()->saveMany([$pao, $pao2]);

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

        $this->assertCount(2, $res->json('data.0.options'));

        $pao->update(['enabled' => false]);

        $this->assertCount(1, $this->sendRequest()->json('data.0.options'));

        $pao->update(['enabled' => true, 'deleted_at' => now()]);

        $this->assertCount(1, $this->sendRequest()->json('data.0.options'));
    }

    /**
     * @test
     * @group apiGet
     */
    public function it_returns_the_associated_product_ids()
    {
        $p = Product::factory()->create();
        $p2 = Product::factory()->create();
        $this->product_attribute->products()->attach([$p->id, $p2->id]);

        $res = $this->sendRequest();

        $res->assertJsonStructure([
            'data' => [
                [
                    'product_ids'
                ]
            ]
        ]);

        $this->assertCount(2, $res->json('data.0.product_ids'));

        $p2->update(['deleted_at' => now()]);

        $this->assertCount(1, $this->sendRequest()->json('data.0.product_ids'));
    }

    protected function sendRequest() : \Illuminate\Testing\TestResponse
    {
        return $this->getJson(route('api.product_attributes.get', ['id' => $this->product_attribute->id]));
    }
}
