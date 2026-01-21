<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Storage;
class GoogleGmailOAuthController extends Controller
{
    private const TOKEN_PATH = 'google/gmail_token.json';

    private function makeClient(): GoogleClient
    {
        $client = new GoogleClient();
        $client->setClientId(config('services.google_gmail.client_id'));
        $client->setClientSecret(config('services.google_gmail.client_secret'));
        $client->setRedirectUri(config('services.google_gmail.redirect'));

        $client->setScopes([
            'https://www.googleapis.com/auth/gmail.send',
        ]);

        // Critical to receive a refresh token (offline access) the FIRST time.
        $client->setAccessType('offline');

        // Strongly recommended to ensure consent screen appears and refresh_token is returned
        // especially if you've authorized before.
        $client->setPrompt('consent');

        return $client;
    }

    public function start()
    {
        $client = $this->makeClient();

        // Optional: basic anti-CSRF state value
        $state = bin2hex(random_bytes(16));
        session(['google_oauth_state' => $state]);
        $client->setState($state);

        return redirect()->away($client->createAuthUrl());
    }

    public function callback(Request $request)
    {
        $client = $this->makeClient();

        // Validate state if you set it
        $expectedState = session('google_oauth_state');
        if ($expectedState && $request->string('state')->toString() !== $expectedState) {
            abort(403, 'Invalid OAuth state.');
        }

        if ($request->filled('error')) {
            abort(400, 'Google OAuth error: '.$request->string('error')->toString());
        }

        $code = $request->string('code')->toString();
        if (!$code) {
            abort(400, 'Missing authorization code.');
        }

        // Exchange code for tokens
        $token = $client->fetchAccessTokenWithAuthCode($code);

        if (isset($token['error'])) {
            abort(400, 'Token exchange failed: '.$token['error'].' - '.($token['error_description'] ?? ''));
        }

        // IMPORTANT:
        // Google often returns refresh_token only on first successful consent.
        // If refresh_token is missing but you already stored it previously, keep the old one.
        $existing = Storage::disk('local')->exists(self::TOKEN_PATH)
            ? json_decode(Storage::disk('local')->get(self::TOKEN_PATH), true)
            : null;

        if (empty($token['refresh_token']) && !empty($existing['refresh_token'])) {
            $token['refresh_token'] = $existing['refresh_token'];
        }

        // Persist token JSON to storage/app/google/gmail_token.json
        Storage::disk('local')->put(self::TOKEN_PATH, json_encode($token, JSON_PRETTY_PRINT));

        return response()->json([
            'ok' => true,
            'stored_at' => storage_path('app/'.self::TOKEN_PATH),
            'has_refresh_token' => !empty($token['refresh_token']),
            'note' => !empty($token['refresh_token'])
                ? 'Refresh token stored. You can now send via Gmail API without re-consent.'
                : 'No refresh token returned. If this is your first setup, revoke app access and retry.',
        ]);
    }
}
