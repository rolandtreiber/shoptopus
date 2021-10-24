<?php

namespace App\Observers;

use App\Helpers\GeneralHelper;
use App\Models\AccessToken;

class AccessTokenObserver
{
    public function creating(AccessToken $accessToken)
    {
        do {
            $token = GeneralHelper::generateRandomString(20);
        } while (AccessToken::where('token', $token)->first());
        $accessToken->token = $token;
    }
}
