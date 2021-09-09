<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ProductCategoryController
 */
class ProductCategoryControllerTest extends TestCase
{
    /**
     * @test
     */
    public function index_responds_with()
    {
        $response = $this->get(route('product-category.index'));

        $response->assertNoContent();
    }
}
