<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;

/**
 * @see \App\Http\Controllers\AdminController
 */
class AdminControllerTest extends TestCase
{
    /**
     * @test
     */
    public function index_responds_with()
    {
        $response = $this->get(route('admin.index'));

        $response->assertNoContent();
    }


    /**
     * @test
     */
    public function dashboard_responds_with()
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertNoContent();
    }
}
