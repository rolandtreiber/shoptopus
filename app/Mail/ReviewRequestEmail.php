<?php

namespace App\Mail;

use App\Mail\Admin\ArchiveableEmail;
use App\Models\AccessToken;
use App\Models\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class ReviewRequestEmail extends ArchiveableEmail
{
    use Queueable, SerializesModels;

    public AccessToken $token;
    public Order $order;
    public User $user;

    /**
     * Create a new message instance.
     */
    public function __construct(AccessToken $token, Order $order, User $user)
    {
        $this->token = $token;
        $this->order = $order;
        $this->user = $user;
    }

    public function build(): static
    {
        $address = config('constants.support_email');
        $subject = 'Leave a review';
        $name = config('app.app_name');

        return $this->view('email.review-request', [
            'user' => $this->user,
            'token' => $this->token,
            'order' => $this->order
        ])
            ->from($address, $name)
            ->replyTo($address, $name)
            ->subject($subject);
    }
}
