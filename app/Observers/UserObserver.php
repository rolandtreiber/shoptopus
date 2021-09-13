<?php

namespace App\Observers;

use App\Models\Cart;
use App\Models\User;

class UserObserver
{

    /**
     * @param User $user
     */
    public function saved(User $user)
    {
        $cart = new Cart();
        $cart->user_id = $user->id;
        $cart->save();
    }

}
