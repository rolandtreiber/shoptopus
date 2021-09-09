<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;

/**
 * @see \App\Http\Controllers\DiscountRuleController
 */
class DiscountRuleControllerTest extends TestCase
{
    /**
     * @test
     */
    public function index_responds_with()
    {
        $response = $this->get(route('discount-rule.index'));

        $response->assertNoContent();
    }
}
