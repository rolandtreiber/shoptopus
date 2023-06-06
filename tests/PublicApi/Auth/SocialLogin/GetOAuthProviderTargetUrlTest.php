<?php

namespace Tests\PublicApi\Auth\SocialLogin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetOAuthProviderTargetUrlTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     *
     * @group apiGet
     */
    public function it_requires_a_provider_query_parameter(): void
    {
        $this->expectException(\Illuminate\Routing\Exceptions\UrlGenerationException::class);
        $this->sendRequest(null);
    }

    /**
     * @test
     *
     * @group apiGet
     */
    public function it_accepts_only_the_predefined_providers(): void
    {
        $this->sendRequest('invalidprovider')
            ->assertJsonValidationErrors(['provider']);
    }

    /**
     * @test
     *
     * @group apiGet
     */
    public function it_returns_a_target_url_for_facebook(): void
    {
        $this->sendRequest('facebook')
            ->assertJsonStructure(['data' => ['targetUrl']]);
    }

    /**
     * @test
     *
     * @group apiGet
     */
    public function it_returns_a_target_url_for_google(): void
    {
        $this->sendRequest('google')
            ->assertJsonStructure(['data' => ['targetUrl']]);
    }

    protected function sendRequest($provider): \Illuminate\Testing\TestResponse
    {
        return $this->getJson(route('api.auth.getOAuthProviderTargetUrl', ['provider' => $provider]));
    }
}
