<?php

namespace App\Events\Admin;

use App\Enums\AccessTokenTypes;
use App\Mail\Admin\UserPasswordResetEmail;
use App\Models\AccessToken;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class UserPasswordReset
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public string $token;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        $token = new AccessToken();
        $token->type = AccessTokenTypes::PasswordReset;
        $token->expiry = Carbon::now()->addHours(24);
        $token->issuer_user_id = null;
        $token->user_id = $user->id;
        $token->save();
        $this->token = $token;
        Mail::to(trim($user->email))->send(new UserPasswordResetEmail($user, $token->token));
    }
}
