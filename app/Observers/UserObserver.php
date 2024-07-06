<?php

namespace App\Observers;

use App\Enums\RandomStringMode;
use App\Helpers\GeneralHelper;
use App\Models\Cart;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserObserver
{
    /**
     * Handle events after all transactions are committed.
     */
    public bool $afterCommit = true;

    public function creating(User $user): void
    {
        do {
            $reference = GeneralHelper::generateRandomString(12, RandomStringMode::UppercaseAndNumbers);
        } while (User::where('client_ref', $reference)->first());
        $user->client_ref = $reference;
        if ($user->temporary) {
            $user->email = $user->email."-".$reference;
            $user->password = Hash::make($reference);
        }
    }

    public function saving(User $user): void
    {
        $firstName = $user->first_name;
        $lastName = $user->last_name;
        $prefix = $user->prefix ?? '';

        $user->name = trim($prefix.' '.$firstName.' '.$lastName);

        $user->initials = substr($firstName, 0, 1).substr($lastName, 0, 1);
    }

    public function created(User $user): void
    {
        $cart = new Cart();
        $cart->user_id = $user->id;
        $cart->save();
    }
}
