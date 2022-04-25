<?php

namespace Tests\PublicApi\ProductAttribute;

use Tests\TestCase;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeOption;
use App\Services\Local\Error\ErrorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repositories\Local\ProductAttribute\ProductAttributeRepository;

class GetAllProductAttributesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @group apiGetAll
     */
    public function it_returns_the_correct_format()
    {
        $this->sendRequest()
            ->assertJsonStructure([
                'message',
                'data',
                'next',
                'records',
                'total_records'
            ]);
    }

    /**
     * @test
     * @group apiGetAll
     */
    public function it_returns_all_required_fields()
    {
        $pa = ProductAttribute::factory()->create();
        $pa2 = ProductAttribute::factory()->create();
        ProductAttributeOption::factory()->create(['product_attribute_id' => $pa->id]);
        ProductAttributeOption::factory()->create(['product_attribute_id' => $pa2->id]);
        $res = $this->sendRequest();

        $res->assertJsonStructure([
            'data' => [
                (new ProductAttributeRepository(new ErrorService, new ProductAttribute))->getSelectableColumns(false)
            ]
        ]);

        $this->assertCount(2, $res->json('data'));
    }

    /**
     * @test
     * @group apiGetAll
     */
    public function soft_deleted_and_disabled_product_attributes_are_not_returned()
    {
        ProductAttribute::factory()->count(2)->create(['deleted_at' => now()]);
        ProductAttribute::factory()->create(['enabled' => false]);

        $this->assertEmpty($this->sendRequest()->json('data'));
    }

    /**
     * @test
     * @group apiGetAll
     */
    public function it_returns_the_count()
    {
        $pa = ProductAttribute::factory()->count(2)->create();
        ProductAttributeOption::factory()->create(['product_attribute_id' => $pa[0]->id]);
        ProductAttributeOption::factory()->create(['product_attribute_id' => $pa[1]->id]);

        $this->assertEquals(2, $this->signIn()->sendRequest()->json('total_records'));
    }

    /**
     * @test
     * @group apiGetAll
     */
    public function it_returns_the_associated_options()
    {
        $pa = ProductAttribute::factory()->create();
        $pao = ProductAttributeOption::factory()->create();
        $pao2 = ProductAttributeOption::factory()->create();
        $pa->options()->saveMany([$pao, $pao2]);

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
     * @group apiGetAll
     */
    public function attributes_without_options_are_excluded()
    {
        ProductAttribute::factory()->create();

        $res = $this->sendRequest();

        $this->assertEmpty($res->json('data'));
    }

    /**
     * @test
     * @group apiGetAll
     */
    public function it_returns_the_associated_product_ids()
    {
        $pa = ProductAttribute::factory()->create();
        ProductAttributeOption::factory()->create(['product_attribute_id' => $pa->id]);
        $p = Product::factory()->create();
        $p2 = Product::factory()->create();
        $pa->products()->attach([$p->id, $p2->id]);

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

    /**
     * @test
     * @group apiGetAll
     */
    public function product_attributes_can_be_filtered_by_id()
    {
        $pas = ProductAttribute::factory()->count(3)->create();
        ProductAttributeOption::factory()->create(['product_attribute_id' => $pas[0]->id]);
        ProductAttributeOption::factory()->create(['product_attribute_id' => $pas[1]->id]);
        ProductAttributeOption::factory()->create(['product_attribute_id' => $pas[2]->id]);

        $res = $this->signIn()->sendRequest(['filter[id]' => $pas[0]->id]);

        $this->assertCount(1, $res->json('data'));
        $this->assertEquals($pas[0]->id, $res->json('data.0.id'));
    }

    /**
     * @test
     * @group apiGetAll
     */
    public function filters_can_accept_multiple_parameters()
    {
        $pas = ProductAttribute::factory()->count(3)->create();
        ProductAttributeOption::factory()->create(['product_attribute_id' => $pas[0]->id]);
        ProductAttributeOption::factory()->create(['product_attribute_id' => $pas[1]->id]);
        ProductAttributeOption::factory()->create(['product_attribute_id' => $pas[2]->id]);

        $res = $this->signIn()->sendRequest(['filter[id]' => implode(',', [$pas[0]->id, $pas[1]->id])]);

        $this->assertCount(2, $res->json('data'));
        $this->assertEquals($pas[0]->id, $res->json('data.0.id'));
        $this->assertEquals($pas[1]->id, $res->json('data.1.id'));
    }

    protected function sendRequest($data = []) : \Illuminate\Testing\TestResponse
    {
        return $this->getJson(route('api.product_attributes.getAll', $data));
    }
}
