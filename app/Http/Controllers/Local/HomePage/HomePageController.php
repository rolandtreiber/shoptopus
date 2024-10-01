<?php

namespace App\Http\Controllers\Local\HomePage;

use App\Http\Controllers\Controller;
use App\Http\Resources\HomePage\HomePageResource;
use App\Services\Local\HomePage\HomePageServiceInterface;
use Illuminate\Http\Request;

class HomePageController extends Controller
{
    private HomePageServiceInterface $homePageService;

    public function __construct(HomePageServiceInterface $homePageService)
    {
        $this->homePageService = $homePageService;
    }

    public function index(Request $request): HomePageResource
    {
        $user = $request->user('api');
        return new HomePageResource($this->homePageService->getHomePage($user));
    }
}
