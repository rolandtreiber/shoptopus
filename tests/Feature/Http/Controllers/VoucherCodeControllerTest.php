<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;

/**
 * @see \App\Http\Controllers\VoucherCodeController
 */
class VoucherCodeControllerTest extends TestCase
{
    /**
     * @test
     */
    public function index_responds_with()
    {
        $response = $this->get(route('voucher-code.index'));

        $response->assertNoContent();
    }
}
