<?php

namespace Tests\Feature\Http\Controllers;

use App\DeliveryType;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\DeliveryRuleController
 */
class DeliveryRuleControllerTest extends TestCase
{
    use AdditionalAssertions, WithFaker;

    /**
     * @test
     */
    public function index_responds_with()
    {
        $response = $this->get(route('delivery-rule.index'));

        $response->assertNoContent();
    }


    /**
     * @test
     */
    public function store_uses_form_request_validation()
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\DeliveryRuleController::class,
            'store',
            \App\Http\Requests\DeliveryRuleStoreRequest::class
        );
    }

    /**
     * @test
     */
    public function store_behaves_as_expected()
    {
        $delivery_type = DeliveryType::factory()->create();

        $response = $this->post(route('delivery-rule.store'), [
            'delivery_type_id' => $delivery_type->id,
        ]);
    }
}
