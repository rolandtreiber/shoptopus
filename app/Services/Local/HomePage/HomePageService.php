<?php

namespace App\Services\Local\HomePage;

use App\Models\User;
use App\Repositories\Local\HomePage\HomePageRepositoryInterface;

class HomePageService implements HomePageServiceInterface
{
    private HomePageRepositoryInterface $homePageRepository;
    public function __construct(HomePageRepositoryInterface $homePageRepository)
    {
        $this->homePageRepository = $homePageRepository;
    }
    public function getHomePage(User|null $user): array
    {
        return $this->homePageRepository->getHomePage($user);
    }
}