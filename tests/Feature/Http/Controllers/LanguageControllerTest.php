<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;

/**
 * @see \App\Http\Controllers\LanguageController
 */
class LanguageControllerTest extends TestCase
{
    /**
     * @test
     */
    public function index_responds_with()
    {
        $response = $this->get(route('language.index'));

        $response->assertNoContent();
    }
}
