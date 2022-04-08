<?php

namespace App\Services\Local\Auth;

use Google_Client;
use App\Models\User;
use App\Models\SocialAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as ProviderUser;
use App\Services\Local\User\UserServiceInterface;
use App\Services\Local\Error\ErrorServiceInterface;

class SocialAccountService implements SocialAccountServiceInterface
{
    private ErrorServiceInterface $errorService;
    private UserServiceInterface $userService;

    public function __construct(ErrorServiceInterface $errorService, UserServiceInterface $userService)
    {
        $this->errorService = $errorService;
        $this->userService = $userService;
    }

    /**
     * Get the target url to the Auth provider's authentication page
     *
     * @param string $provider
     * @return string
     * @throws \Exception
     */
    public function getOAuthProviderTargetUrl(string $provider) : string
    {
        try {
            return Socialite::driver($provider)->stateless()->redirect()->getTargetUrl();
        } catch (\Exception | \Error $e) {
            $this->errorService->logException($e);
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.auth.getOAuthProviderTargetUrl'));
        }
    }

    /**
     * Handle the Auth provider's callback
     *
     * @param array $payload
     * @return array
     * @throws \Exception
     */
    public function handleOAuthProviderCallback(array $payload) : array
    {
        try {
            $provider = $payload['provider'];
            $code = $payload['code'];

            $socialiteUser = Socialite::driver($provider)->stateless()->userFromToken(
                $this->getAccessTokenFromProvider($provider, $code)
            );

            if (is_null($socialiteUser)) {
                throw new \Exception('Something went wrong. User could not be found');
            }

            return $this->findOrCreate($socialiteUser, $provider);
        } catch (\Exception | \Error $e) {
            throw new \Exception($e->getMessage(), Config::get('api_error_codes.services.auth.handleOAuthProviderCallback'));
        }
    }

    /**
     * Find or create user instance by provider user instance and provider name
     *
     * @param ProviderUser $providerUser
     * @param string $provider
     * @return array
     * @throws \Exception
     */
    public function findOrCreate(ProviderUser $providerUser, string $provider) : array
    {
        $socialAccount = SocialAccount::where('provider_name', $provider)
            ->where('provider_id', $providerUser->getId())
            ->first();

        if ($socialAccount) {
            return $socialAccount->user->toArray();
        } else {
            $user = null;

            $email = $providerUser->getEmail();

            if ($email) {
                $user = User::where('email', $email)->first();
            }

            if(!$user) {
                $user_data = $this->getPayload($providerUser);
                $user = $this->userService->post($user_data);

                if(!($user instanceof User)) {
                    $user = User::findOrFail($user['id']);
                }
            }

            DB::table('social_accounts')->insert([
                'user_id' => $user->id,
                'provider_id' => $providerUser->getId(),
                'provider_name' => $provider,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return $user->toArray();
        }
    }

    /**
     * Get a payload for creating a new social account
     *
     * @param ProviderUser $providerUser
     * @return array
     * @throws \Exception
     */
    public function getPayload(ProviderUser $providerUser) : array
    {
        $name = $this->splitUserName($providerUser->getName());

        return [
            "first_name" => $name['first_name'],
            "last_name" => $name['last_name'],
            "email" => $providerUser->getEmail(),
            "phone" => null,
            "password" => null
        ];
    }

    /**
     * Get an access token from the given provider
     *
     * @param string $provider
     * @param string $authorization_code
     * @return string
     * @throws \Exception
     */
    protected function getAccessTokenFromProvider(string $provider, string $authorization_code) : string
    {
        $accessToken = null;

        if ($provider === 'facebook') {
            $response = Http::get('https://graph.facebook.com/v12.0/oauth/access_token?', [
                'client_id' => Config::get('services.facebook.client_id'),
                'redirect_uri' => Config::get('services.facebook.redirect'),
                'client_secret' => Config::get('services.facebook.client_secret'),
                'code' => $authorization_code
            ])->json(); // access_token, token_type, expires_in

            if (isset($response['error'])) {
                throw new \Exception($response['error']['message']);
            }

            $accessToken = $response['access_token'];
        } else if ($provider === 'google') {
            // @see https://developers.google.com/identity/protocols/oauth2/web-server#php_2
            // @see https://www.oauth.com/oauth2-servers/signing-in-with-google/getting-an-id-token/
            $client = new Google_Client([
                'client_id' => Config::get('services.google.client_id'),
                'client_secret' => Config::get('services.google.client_secret')
            ]);

            $client->addScope(['openid', 'profile', 'email']);
            $client->setRedirectUri(Config::get('services.google.redirect'));
            $client->setAccessType('offline');
            $client->setApprovalPrompt("consent");
            $client->setIncludeGrantedScopes(true);

            // Make sure to urldecode the code
            $response = $client->fetchAccessTokenWithAuthCode(urldecode($authorization_code));

            $accessToken = $response['access_token'];
        }

        return $accessToken;
    }

    public function splitUserName($name) {
        $parts = [];

        while ( strlen( trim($name)) > 0 ) {
            $name = trim($name);
            $string = preg_replace('#.*\s([\w-]*)$#', '$1', $name);
            $parts[] = $string;
            $name = trim( preg_replace('#'.preg_quote($string,'#').'#', '', $name ) );
        }

        if (empty($parts)) {
            return false;
        }

        $parts = array_reverse($parts);
        $name = [];
        $name['first_name'] = $parts[0];
        $name['middle_name'] = (isset($parts[2])) ? $parts[1] : '';
        $name['last_name'] = (isset($parts[2])) ? $parts[2] : ($parts[1] ?? '');

        return $name;
    }
}
