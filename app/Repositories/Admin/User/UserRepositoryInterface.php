<?php

namespace App\Repositories\Admin\User;

use App\Models\User;

interface UserRepositoryInterface
{
    public function triggerNewUserRegistrationNotification(User $user): bool;

    public function invite(String $email, String $role): bool;
}
