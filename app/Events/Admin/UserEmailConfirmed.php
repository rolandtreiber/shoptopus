<?php

namespace App\Events\Admin;

use App\Models\User;

class UserEmailConfirmed
{
    public $user;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
