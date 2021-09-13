<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailAddressConfirmedEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $address = config('constants.support_email');
        $subject = "Email confirmation successful";
        $name = config('app.app_name');

        return $this->view('email.email-confirmed', [
            'user' => $this->user
        ])
            ->from($address, $name)
            ->replyTo($address, $name)
            ->subject($subject);
    }
}
