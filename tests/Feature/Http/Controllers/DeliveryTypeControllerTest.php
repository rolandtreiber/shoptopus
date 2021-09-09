<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;

/**
 * @see \App\Http\Controllers\DeliveryTypeController
 */
class DeliveryTypeControllerTest extends TestCase
{
    /**
     * @test
     */
    public function index_responds_with()
    {
        $response = $this->get(route('delivery-type.index'));

        $response->assertNoContent();
    }
}
