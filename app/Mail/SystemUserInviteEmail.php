<?php

namespace App\Mail;

use App\Models\AccessToken;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SystemUserInviteEmail extends Mailable
{
    use Queueable, SerializesModels;

    public AccessToken $token;
    public User $user;

    /**
     * Create a new message instance.
     */
    public function __construct(AccessToken $token, User $user)
    {
        $this->token = $token;
        $this->user = $user;
    }

    public function build(): static
    {
        $secureSignupUrl = "admin/signup?token=".$this->token->token;
        $user = $this->user;
        $fromAddress = config('constants.support_email');

        return $this->view('email.signup-request', [
            'secureSignupUrl' => $secureSignupUrl,
            'user' => $this->user,
            'token' => $this->token,
        ])
            ->from($fromAddress, config("app.name"))
            ->replyTo($user->email, $user->name)
            ->subject("You've been invited to sign up!");
    }
}
