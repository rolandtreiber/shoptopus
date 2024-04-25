<?php

namespace App\Http\Controllers\Rating;

use App\Http\Controllers\Controller;
use App\Http\Requests\Rating\RatingsRequest;
use App\Models\Product;
use App\Services\Local\Rating\RatingServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RatingController extends Controller
{

    private RatingServiceInterface $ratingService;

    public function __construct(RatingServiceInterface $ratingService)
    {
        $this->ratingService = $ratingService;
    }

    /**
     * Get all models
     */
    public function getAllForProduct(Product $product, RatingsRequest $request): JsonResponse
    {
        try {
            [$filters, $page_formatting] = $this->getFiltersAndPageFormatting($request);

            return response()->json($this->getResponse($page_formatting, $this->ratingService->getRatingsForProduct($product, $request->ratings ?: [], $request->language_prefix, $page_formatting, $filters), $request));
        } catch (\Exception|\Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }
    }


}
