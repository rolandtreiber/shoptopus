<?php

namespace App\Http\Controllers\Local\ProductTag;

use App\Http\Controllers\Controller;
use App\Services\Local\ProductTag\ProductTagServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductTagController extends Controller
{
    private ProductTagServiceInterface $productTagService;

    public function __construct(ProductTagServiceInterface $productTagService)
    {
        $this->productTagService = $productTagService;
    }

    /**
     * Get all models
     */
    public function getAll(Request $request): JsonResponse
    {
        try {
            [$filters, $page_formatting] = $this->getFiltersAndPageFormatting($request);
            return response()->json($this->getResponse(
                $page_formatting,
                $this->productTagService->getAll($page_formatting, $filters),
                $request
            ));
        } catch (\Exception|\Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }
    }

    /**
     * Get a single model
     */
    public function get(Request $request, string $id): JsonResponse
    {
        try {
            return response()->json($this->getResponse([], $this->productTagService->get($id), $request));
        } catch (\Exception|\Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }
    }
}
