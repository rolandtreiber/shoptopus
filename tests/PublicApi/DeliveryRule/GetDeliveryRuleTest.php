<?php

namespace Tests\PublicApi\DeliveryRule;

use Tests\TestCase;
use App\Models\DeliveryRule;
use App\Services\Local\Error\ErrorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repositories\Local\DeliveryRule\DeliveryRuleRepository;

class GetDeliveryRuleTest extends TestCase
{
    use RefreshDatabase;

    protected $delivery_rule;

    public function setUp() : void
    {
        parent::setUp();

        $this->delivery_rule = DeliveryRule::factory()->create();
    }

//    /**
//     * @test
//     * @group apiGet
//     */
//    public function unauthenticated_users_are_not_allowed_to_get_delivery_rules()
//    {
//        $unAuthenticatedRes = $this->sendRequest()->json();
//
//        $this->assertEquals('Unauthenticated.', $unAuthenticatedRes['developer_message']);
//        $this->assertEquals('Sorry there was a system error, the administrator has been informed.', $unAuthenticatedRes['user_message']);
//    }

//    /**
//     * @test
//     * @group apiGet
//     */
//    public function unauthorized_users_are_not_allowed_to_get_delivery_rules()
//    {
//        $res = $this->sendRequest()->json();
//
//        $this->assertEquals('This action is unauthorized.', $res['developer_message']);
//        $this->assertEquals('Sorry there was a system error, the administrator has been informed.', $res['user_message']);
//    }

    /**
     * @test
     * @group apiGet
     */
    public function it_can_return_a_delivery_rule_by_its_id()
    {
        $this->sendRequest()->assertOk();
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
                    $this->getModelRepo()->getSelectableColumns(false)
                ]
            ]);
    }

    /**
     * @test
     * @group apiGet
     */
    public function it_returns_the_associated_enabled_delivery_type()
    {
        $res = $this->sendRequest();

        $res->assertJsonStructure([
            'data' => [
                [
                    'delivery_type' => [
                        'id',
                        'name',
                        'description',
                        'price'
                    ]
                ]
            ]
        ]);

        $this->delivery_rule->delivery_type()->update(['deleted_at' => now()]);

        $this->assertNull($this->sendRequest()->json('data.0.delivery_type'));

        $this->delivery_rule->delivery_type()->update(['deleted_at' => null, 'enabled' => false]);

        $this->assertNull($this->sendRequest()->json('data.0.delivery_type'));
    }

    protected function getModelRepo() : DeliveryRuleRepository
    {
        return new DeliveryRuleRepository(new ErrorService, new DeliveryRule);
    }

    protected function sendRequest() : \Illuminate\Testing\TestResponse
    {
        return $this->getJson(route('api.delivery_rules.get', ['id' => $this->delivery_rule->id]));
    }
}
