<?php

namespace App\Observers;

use App\Models\Cart;
use App\Models\User;
use App\Helpers\GeneralHelper;
use App\Enums\RandomStringModes;

class UserObserver
{
    /**
     * Handle events after all transactions are committed.
     *
     * @var bool
     */
    public bool $afterCommit = true;

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
        $prefix = $user->prefix ?? '';

        $user->name = trim($prefix . ' ' . $firstName . ' ' . $lastName);

        $user->initials = substr($firstName, 0, 1) . substr($lastName, 0, 1);
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
