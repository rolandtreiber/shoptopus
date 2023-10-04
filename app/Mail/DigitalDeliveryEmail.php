<?php

namespace App\Mail;

use App\Mail\Admin\ArchiveableEmail;
use App\Models\AccessToken;
use App\Models\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class DigitalDeliveryEmail extends ArchiveableEmail
{
    use Queueable, SerializesModels;

    public Order $order;
    public User $user;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order, User $user)
    {
        $this->order = $order;
        $this->user = $user;
    }

    public function build(): static
    {
        $address = config('constants.support_email');
        $subject = 'Your files are ready to download';
        $name = config('app.app_name');

        return $this->view('email.digital-delivery', [
            'user' => $this->user,
            'order' => $this->order
        ])
            ->from($address, $name)
            ->replyTo($address, $name)
            ->subject($subject);
    }
}
