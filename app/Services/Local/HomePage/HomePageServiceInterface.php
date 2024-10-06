<?php

namespace App\Services\Local\HomePage;

use App\Models\User;

interface HomePageServiceInterface
{

    public function getHomePage(User|null $user): array;
}