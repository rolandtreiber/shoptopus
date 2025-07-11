<?php

namespace App\Listeners;

use App\Enums\AccessTokenType;
use App\Events\OrderPlacedEvent;
use App\Mail\DigitalDeliveryEmail;
use App\Mail\OrderCreatedEmail;
use App\Models\AccessToken;
use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class OrderPlacedEventListener
{
    /**
     * Handle the event.
     */
    public function handle(OrderPlacedEvent $event): void
    {
        // @phpstan-ignore-next-line
        if ($event->invoice || config('app.env') === 'testing') {
            $now = Carbon::now();
            $token = new AccessToken();
            $token->accessable_type = Invoice::class;
            $token->accessable_id = $event->invoice->id;
            $token->type = AccessTokenType::Invoice;
            $token->user_id = $event->invoice->user_id;
            $token->issuer_user_id = $event->invoice->user_id;
            $token->expiry = $now->addYears(5);
            $token->save();

            $email = str_replace("-".$event->invoice->user->client_ref, "", $event->invoice->user->email);
            Mail::to($email)->send(new OrderCreatedEmail($token, $event->invoice, $event->invoice->user));
        }
    }
}
