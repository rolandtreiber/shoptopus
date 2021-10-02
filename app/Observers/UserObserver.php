<?php

namespace App\Observers;

use App\Enums\RandomStringModes;
use App\Helpers\GeneralHelper;
use App\Models\Cart;
use App\Models\User;

class UserObserver
{
    /**
     * @param User $user
     */
    public function creating(User $user)
    {
        do {
            $reference = GeneralHelper::generateRandomString(8, RandomStringModes::UppercaseAndNumbers);
        } while (User::where('client_ref', $reference)->first());
        $user->client_ref = $reference;
    }

    /**
     * @param User $user
     */
    public function saving(User $user)
    {
        $firstName = $user->first_name;
        $lastName = $user->last_name;
        $prefix = $user->prefix;
        if ($prefix) {
            $user->name = $prefix.' '.$firstName.' '.$lastName;
        } else {
            $user->name = $firstName.' '.$lastName;
        }
        $user->initials = substr($firstName, 0, 1).substr($lastName, 0, 1);
    }

    /**
     * @param User $user
     */
    public function created(User $user)
    {
        $cart = new Cart();
        $cart->user_id = $user->id;
        $cart->save();
    }

}
