<?php

namespace App\Listeners;

use App\Events\OrderStatusUpdatedEvent;
use App\Mail\DigitalDeliveryEmail;
use App\Mail\OrderUpdatedEmail;
use App\Mail\ReviewRequestEmail;
use Illuminate\Support\Facades\Mail;

class OrderStatusUpdatedEventListener
{
    /**
     * Handle the event.
     */
    public function handle(OrderStatusUpdatedEvent $event): void
    {
        $email = str_replace("-" . $event->order->user->client_ref, "", $event->order->user->email);
        Mail::to($email)->send(new OrderUpdatedEmail($event->order));
    }
}
