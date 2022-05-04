<?php

namespace App\Http\Controllers\Product;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Local\Product\ProductServiceInterface;

class ProductController extends Controller
{
    private ProductServiceInterface $productService;

    public function __construct(ProductServiceInterface $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Get all models
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll(Request $request) : \Illuminate\Http\JsonResponse
    {
        try {
            list($filters, $page_formatting) = $this->getFiltersAndPageFormatting($request);

            return response()->json(
                $this->getResponse($page_formatting, $this->productService->getAll($page_formatting, $filters), $request)
            );
        } catch (\Exception | \Error $e) {
            return $this->errorResponse($e, __("error_messages." . $e->getCode()));
        }
    }

    /**
     * Get a single model
     *
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Request $request, string $id) : \Illuminate\Http\JsonResponse
    {
        try {
            return response()->json($this->getResponse([], $this->productService->get($id), $request));
        } catch (\Exception | \Error $e) {
            return $this->errorResponse($e, __("error_messages." . $e->getCode()));
        }
    }

    /**
     * Get a single model by its slug
     *
     * @param \Illuminate\Http\Request
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBySlug(Request $request, string $slug) : \Illuminate\Http\JsonResponse
    {
        try {
            return response()->json($this->getResponse([], $this->productService->getBySlug($slug), $request));
        } catch (\Exception | \Error $e) {
            return $this->errorResponse($e, __("error_messages." . $e->getCode()));
        }
    }
}
