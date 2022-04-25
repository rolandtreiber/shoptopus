<?php

namespace App\Http\Controllers\ProductAttribute;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Local\ProductAttribute\ProductAttributeServiceInterface;

class ProductAttributeController extends Controller
{
    private ProductAttributeServiceInterface $productAttributeService;

    public function __construct(ProductAttributeServiceInterface $productAttributeService)
    {
        $this->productAttributeService = $productAttributeService;
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
            $filters = $this->getAndValidateFilters($request);
            $filters['deleted_at'] = 'null';
            $filters['enabled'] = true;
            $page_formatting = $this->getPageFormatting($request);
            return response()->json($this->getResponse($page_formatting, $this->productAttributeService->getAll($page_formatting, $filters), $request));
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
            return response()->json($this->getResponse([], $this->productAttributeService->get($id), $request));
        } catch (\Exception | \Error $e) {
            return $this->errorResponse($e, __("error_messages." . $e->getCode()));
        }
    }
}
