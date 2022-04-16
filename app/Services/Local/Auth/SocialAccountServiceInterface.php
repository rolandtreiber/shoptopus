<?php

namespace App\Services\Local\Auth;

use Laravel\Socialite\Two\User as ProviderUser;

interface SocialAccountServiceInterface {

    /**
     * Get the target url to the Auth provider's authentication page
     *
     * @param string $provider
     * @return string
     * @throws \Exception
     */
    public function getOAuthProviderTargetUrl(string $provider) : string;

    /**
     * Handle the Auth provider's callback
     *
     * @param array $payload
     * @return array
     * @throws \Exception
     */
    public function handleOAuthProviderCallback(array $payload) : array;

    /**
     * Find or create user instance by provider user instance and provider name
     *
     * @param ProviderUser $providerUser
     * @param string $provider
     * @return array
     * @throws \Exception
     */
    public function findOrCreate(ProviderUser $providerUser, string $provider) : array;

    /**
     * Get a payload for creating a new social account
     *
     * @param ProviderUser $providerUser
     * @return array
     * @throws \Exception
     */
    public function getPayload(ProviderUser $providerUser) : array;
}
