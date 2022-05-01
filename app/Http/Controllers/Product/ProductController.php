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

            return response()->json($this->getResponse($page_formatting, $this->productService->getAll($page_formatting, $filters), $request));
        } catch (\Exception | \Error $e) {
            return $this->errorResponse($e, __("error_messages." . $e->getCode()));
        }
    }

    /**
     * Get all models belonging to a category
     *
     * @param Request $request
     * @param string $product_category_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllInCategory(Request $request, string $product_category_id) : \Illuminate\Http\JsonResponse
    {
        try {
            list($filters, $page_formatting) = $this->getFiltersAndPageFormatting($request);

            $filters['relation'] = [
                'type' => 'belongsToMany',
                'table' => 'product_product_category',
                'local_pivot_key' => 'product_id',
                'foreign_pivot_key' => 'product_category_id',
                'foreign_pivot_value' => $product_category_id
            ];

            return response()->json($this->getResponse(
                $page_formatting,
                $this->productService->getAll($page_formatting, $filters, ['product_categories']),
                $request
            ));
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
