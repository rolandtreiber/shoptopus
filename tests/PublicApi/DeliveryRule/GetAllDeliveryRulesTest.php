<?php

namespace Tests\PublicApi\DeliveryRule;

use Tests\TestCase;
use App\Models\DeliveryRule;
use App\Services\Local\Error\ErrorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repositories\Local\DeliveryRule\DeliveryRuleRepository;

class GetAllDeliveryRulesTest extends TestCase
{
    use RefreshDatabase;

//    /**
//     * @test
//     * @group apiGetAll
//     */
//    public function unauthorized_users_are_not_allowed_to_get_all_their_delivery_rules()
//    {
//        $res = $this->sendRequest()->json();
//
//        $this->assertEquals('Unauthenticated.', $res['developer_message']);
//        $this->assertEquals('Sorry there was a system error, the administrator has been informed.', $res['user_message']);
//    }

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
        DeliveryRule::factory()->count(2)->create();

        $res = $this->sendRequest();

        $res->assertJsonStructure([
            'data' => [
                $this->getModelRepo()->getSelectableColumns(false)
            ]
        ]);

        $this->assertCount(2, $res->json('data'));
    }

    /**
     * @test
     * @group apiGetAll
     */
    public function soft_deleted_delivery_rules_are_not_returned()
    {
        DeliveryRule::factory()->count(2)->create([
            'deleted_at' => now()
        ]);

        $res = $this->sendRequest();

        $this->assertEmpty($res->json('data'));
    }

    /**
     * @test
     * @group apiGetAll
     */
    public function it_returns_the_count()
    {
        DeliveryRule::factory()->count(2)->create();

        $this->assertEquals(2, $this->sendRequest()->json('total_records'));
    }

    /**
     * @test
     * @group apiGetAll
     */
    public function delivery_rules_can_be_filtered_by_id()
    {
        DeliveryRule::factory()->count(3)->create();
        $delivery_rule = DeliveryRule::factory()->create();

        $res = $this->sendRequest(['filter[id]' => $delivery_rule->id]);

        $this->assertCount(1, $res->json('data'));
        $this->assertEquals($delivery_rule->id, $res->json('data.0.id'));
    }

    /**
     * @test
     * @group apiGetAll
     */
    public function filters_can_accept_multiple_parameters()
    {
        DeliveryRule::factory()->count(3)->create();
        $delivery_rule1 = DeliveryRule::factory()->create();
        $delivery_rule2 = DeliveryRule::factory()->create();

        $res = $this->sendRequest(['filter[id]' => implode(',', [$delivery_rule1->id, $delivery_rule2->id])]);

        $this->assertCount(2, $res->json('data'));
        $this->assertEquals($delivery_rule1->id, $res->json('data.0.id'));
        $this->assertEquals($delivery_rule2->id, $res->json('data.1.id'));
    }

    protected function getModelRepo() : DeliveryRuleRepository
    {
        $errorService = new ErrorService;
        return new DeliveryRuleRepository($errorService, new DeliveryRule);
    }

    protected function sendRequest($data = []) : \Illuminate\Testing\TestResponse
    {
        return $this->getJson(route('api.delivery_rules.getAll', $data));
    }
}
