<?php

namespace App\Observers;

use App\Enums\RandomStringMode;
use App\Helpers\GeneralHelper;
use App\Models\AccessToken;

class AccessTokenObserver
{
    public function creating(AccessToken $accessToken)
    {
        do {
            $token = GeneralHelper::generateRandomString(20, RandomStringMode::UppercaseAndNumbers);
        } while (AccessToken::where('token', $token)->first());
        $accessToken->token = $token;
    }
}
