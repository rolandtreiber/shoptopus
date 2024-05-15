<?php

namespace App\Http\Controllers\ProductAttribute;

use App\Http\Controllers\Controller;
use App\Services\Local\ProductAttribute\ProductAttributeServiceInterface;
use Illuminate\Http\Request;

class ProductAttributeController extends Controller
{
    private ProductAttributeServiceInterface $productAttributeService;

    public function __construct(ProductAttributeServiceInterface $productAttributeService)
    {
        $this->productAttributeService = $productAttributeService;
    }

    /**
     * Get all models
     */
    public function getAll(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            [$filters, $page_formatting] = $this->getFiltersAndPageFormatting($request);
            return response()->json($this->getResponse(
                $page_formatting,
                $this->productAttributeService->getAll($page_formatting, $filters),
                $request
            ));
        } catch (\Exception|\Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }
    }

    /**
     * Get all models for a specific product category
     */
    public function getAllForProductCategory(Request $request, string $product_category_id): \Illuminate\Http\JsonResponse
    {
        try {
            [$page_formatting] = $this->getFiltersAndPageFormatting($request);

            return response()->json($this->getResponse(
                $page_formatting,
                $this->productAttributeService->getAllForProductCategory($product_category_id, $page_formatting),
                $request
            ));
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
            return response()->json($this->getResponse([], $this->productAttributeService->get($id), $request));
        } catch (\Exception|\Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }
    }
}
