<?php

namespace App\Helpers;

use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Storage;

class GmailAuthHelper
{
    public static function gmailClientFromStoredToken(): GoogleClient
    {
        $client = new GoogleClient();
        $client->setClientId(config('services.google_gmail.client_id'));
        $client->setClientSecret(config('services.google_gmail.client_secret'));
        $client->setRedirectUri(config('services.google_gmail.redirect'));
        $client->setScopes(['https://www.googleapis.com/auth/gmail.send']);
        $client->setAccessType('offline');

        $tokenPath = 'google/gmail_token.json';
        if (!Storage::disk('local')->exists($tokenPath)) {
            throw new \RuntimeException("Missing token file. Visit /google/oauth/start first.");
        }

        $token = json_decode(Storage::disk('local')->get($tokenPath), true);
        $client->setAccessToken($token);

        if ($client->isAccessTokenExpired()) {
            $refreshToken = $client->getRefreshToken() ?: ($token['refresh_token'] ?? null);
            if (!$refreshToken) {
                throw new \RuntimeException("No refresh_token available. Re-run consent.");
            }

            $newToken = $client->fetchAccessTokenWithRefreshToken($refreshToken);
            if (isset($newToken['error'])) {
                throw new \RuntimeException("Refresh failed: " . $newToken['error']);
            }

            // Preserve refresh_token (Google may omit it on refresh responses)
            $newToken['refresh_token'] = $refreshToken;

            Storage::disk('local')->put($tokenPath, json_encode($newToken, JSON_PRETTY_PRINT));
            $client->setAccessToken($newToken);
        }

        return $client;
    }

}