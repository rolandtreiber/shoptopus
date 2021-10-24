<?php

namespace App\Events\Admin;

use App\Enums\AccessTokenTypes;
use App\Mail\WelcomeEmail;
use App\Models\AccessToken;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class UserSignup
{
    public $user;
    public $token;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        $expiry = Carbon::now()->addMonths(2);
        $confirmToken = new AccessToken();
        $confirmToken->type = AccessTokenTypes::EmailConfirmation;
        $confirmToken->user_id = $user->id;
        $confirmToken->issuer_user_id = $user->id;
        $confirmToken->expiry = $expiry;
        $confirmToken->save();
        $this->token = $confirmToken;
        Mail::to(trim($user->email))->send(new WelcomeEmail($user, $confirmToken->token));
    }

}
