<?php

namespace App\Mail\Admin;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserEmailReconfirmationEmail extends Mailable
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
    public function build()
    {
        $address = config('constants.support_email');
        $subject = "Please confirm your email";
        $name = config('app.app_name');

        return $this->view('email.reconfirm', [
            'emailConfirmationLink' => config('app.frontend_url')."/#/email-confirm/".$this->token,
            'userName' => $this->user->name
        ])
            ->from($address, $name)
            ->replyTo($address, $name)
            ->subject($subject)
            ->with([
                'emailConfirmationLink' => config('app.frontend_url')."/#/email-confirm/".$this->token,
                'userName' => $this->user->name
            ]);
    }
}
