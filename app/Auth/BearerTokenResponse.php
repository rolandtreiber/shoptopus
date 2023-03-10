<?php

namespace App\Auth;

use App\Http\Resources\User\UserDetailsResource;
use App\Models\User;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;

class BearerTokenResponse extends \League\OAuth2\Server\ResponseTypes\BearerTokenResponse
{
    /**
     * Add custom fields to your Bearer Token response here, then override
     * AuthorizationServer::getResponseType() to pull in your version of
     * this class rather than the default.
     *
     * @param  AccessTokenEntityInterface  $accessToken
     * @return array
     */
    protected function getExtraParams(AccessTokenEntityInterface $accessToken): array
    {
        $user = User::find($this->accessToken->getUserIdentifier());

        return [
            'user' => new UserDetailsResource($user),
        ];
    }
}
