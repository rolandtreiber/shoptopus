<?php

namespace App\Http\Controllers\Local\Banner;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Services\Local\Banner\BannerServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    private BannerServiceInterface $bannerService;

    public function __construct(BannerServiceInterface $bannerService)
    {
        $this->bannerService = $bannerService;
    }

    /**
     * Get all models
     */
    public function getAll(Request $request): JsonResponse
    {
        try {
            [$filters, $page_formatting] = $this->getFiltersAndPageFormatting($request);

            return response()->json(
                $this->getResponse($page_formatting, $this->bannerService->getAll(
                    $page_formatting,
                    $filters,
                    []
                ), $request)
            );
        } catch (\Exception|\Error $e) {
            return $this->errorResponse($e, __('error_messages.' . $e->getCode()));
        }
    }

    public function clicked(Banner $banner): void
    {
        $banner->total_clicks = $banner->total_clicks + 1;
        $banner->save();
    }
}
