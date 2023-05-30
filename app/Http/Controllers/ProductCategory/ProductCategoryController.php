<?php

namespace App\Http\Controllers\ProductCategory;

use App\Http\Controllers\Controller;
use App\Services\Local\ProductCategory\ProductCategoryServiceInterface;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    private ProductCategoryServiceInterface $productCategoryService;

    public function __construct(ProductCategoryServiceInterface $productCategoryService)
    {
        $this->productCategoryService = $productCategoryService;
    }

    /**
     * Get all models
     */
    public function getAll(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            [$filters, $page_formatting] = $this->getFiltersAndPageFormatting($request);

            $filters['parent_id'] = 'null';

            return response()->json($this->getResponse($page_formatting, $this->productCategoryService->getAll($page_formatting, $filters), $request));
        } catch (\Exception|\Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }
    }

    /**
     * Get a single model
     */
    public function get(Request $request, string $id): \Illuminate\Http\JsonResponse
    {
        try {
            return response()->json($this->getResponse([], $this->productCategoryService->get($id), $request));
        } catch (\Exception|\Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }
    }

    /**
     * Get a single model by its slug
     *
     * @param \Illuminate\Http\Request
     */
    public function getBySlug(Request $request, string $slug): \Illuminate\Http\JsonResponse
    {
        try {
            return response()->json($this->getResponse([], $this->productCategoryService->getBySlug($slug), $request));
        } catch (\Exception|\Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }
    }
}
