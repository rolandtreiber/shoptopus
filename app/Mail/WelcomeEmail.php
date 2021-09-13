<?php

namespace App\Mail;

use App\Enums\AccessTokenTypes;
use App\Models\AccessToken;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $token;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): WelcomeEmail
    {

        $address = config('app.support_email');
        $subject = "Welcome to " . config('app.app_name');
        $name = config('app.app_name');

        return $this->view('email.welcome', [
            'emailConfirmationLink' => config('app.frontend_url')."/email-confirm/".$this->token,
            'userName' => $this->user->name
        ])
            ->from($address, $name)
            ->replyTo($address, $name)
            ->subject($subject);
    }
}
