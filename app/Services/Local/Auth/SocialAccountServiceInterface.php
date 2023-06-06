<?php

namespace App\Services\Local\Auth;

use Laravel\Socialite\Two\User as ProviderUser;

interface SocialAccountServiceInterface
{
    /**
     * Get the target url to the Auth provider's authentication page
     *
     *
     * @throws \Exception
     */
    public function getOAuthProviderTargetUrl(string $provider): string;

    /**
     * Handle the Auth provider's callback
     *
     *
     * @throws \Exception
     */
    public function handleOAuthProviderCallback(array $payload): array;

    /**
     * Find or create user instance by provider user instance and provider name
     *
     *
     * @throws \Exception
     */
    public function findOrCreate(ProviderUser $providerUser, string $provider): array;

    /**
     * Get a payload for creating a new social account
     *
     *
     * @throws \Exception
     */
    public function getPayload(ProviderUser $providerUser): array;
}
