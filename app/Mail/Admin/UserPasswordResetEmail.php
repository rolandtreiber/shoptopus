<?php

namespace App\Mail\Admin;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserPasswordResetEmail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $token;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, string $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): UserPasswordResetEmail
    {
        $address = config('mail.from.address');
        $subject = "Reset your password";
        $name = config('mail.from.name');
        $resetUrl = config('app.frontend_url_admin')."/reset-password/".$this->token;

        return $this->view('email.password-reset', [
            'user' => $this->user,
            'resetUrl' => $resetUrl
        ])
            ->from($address, $name)
            ->replyTo($address, $name)
            ->subject($subject);
    }
}
