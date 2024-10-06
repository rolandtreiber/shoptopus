<?php

namespace App\Repositories\Local\HomePage;

use App\Models\User;

interface HomePageRepositoryInterface
{

    public function getHomePage(User|null $user): array;

}