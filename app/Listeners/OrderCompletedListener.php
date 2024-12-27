<?php

namespace App\Listeners;

use App\Enums\AccessTokenType;
use App\Events\OrderCompletedEvent;
use App\Mail\DigitalDeliveryEmail;
use App\Mail\ReviewRequestEmail;
use App\Models\AccessToken;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class OrderCompletedListener
{
    /**
     * Handle the event.
     */
    public function handle(OrderCompletedEvent $event): void
    {
        if ($event->order->invoice || config('app.env') === 'testing') {
            $now = Carbon::now();
            $token = new AccessToken();
            $token->accessable_type = Order::class;
            $token->accessable_id = $event->order->id;
            $token->type = AccessTokenType::Review;
            $token->user_id = $event->order->user->id;
            $token->issuer_user_id = $event->order->user->id;
            $token->expiry = $now->addYear();
            $token->save();
            $email = str_replace("-".$event->order->user->client_ref, "", $event->order->user->email);
            Mail::to($email)->send(new ReviewRequestEmail($token, $event->order, $event->order->user));
            if ($event->order->hasVirtualProduct()) {
                Mail::to($email)->send(new DigitalDeliveryEmail($event->order, $event->order->user));
            }
        }
    }
}
