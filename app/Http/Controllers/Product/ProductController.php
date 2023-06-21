<?php

namespace App\Http\Controllers\Product;

use App\Enums\UserInteractionType;
use App\Events\UserInteraction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\FavoriteProductRequest;
use App\Http\Requests\Product\ProductAvailableAttributeOptionsRequest;
use App\Models\Product;
use App\Models\User;
use App\Repositories\Local\Product\ProductRepositoryInterface;
use App\Services\Local\Product\ProductServiceInterface;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private ProductServiceInterface $productService;

    private ProductRepositoryInterface $productRepository;

    public function __construct(ProductServiceInterface $productService, ProductRepositoryInterface $productRepository)
    {
        $this->productService = $productService;
        $this->productRepository = $productRepository;
    }

    /**
     * Get all models
     */
    public function getAll(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            [$filters, $page_formatting] = $this->getFiltersAndPageFormatting($request);
            if (Auth()->user()) {
                $user = Auth()->user();
                event(new UserInteraction(UserInteractionType::Browse, User::class, $user->id));
            }

            return response()->json(
                $this->getResponse($page_formatting, $this->productService->getAll(
                    $page_formatting,
                    $filters,
                    ['product_variants']
                ), $request)
            );
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
            return response()->json($this->getResponse([], $this->productService->get($id), $request));
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
            return response()->json($this->getResponse([], $this->productService->getBySlug($slug), $request));
        } catch (\Exception|\Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }
    }

    /**
     * Favorite a single model
     */
    public function favorite(FavoriteProductRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            return response()->json($this->postResponse($this->productService->favorite($request->validated()['productId'])));

        } catch (\Exception|\Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }
    }

    /**
     * @param  string  $slug
     */
    public function getAvailableAttributeOptionsForProduct(Product $product, ProductAvailableAttributeOptionsRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            return response()->json($this->productRepository->getAvailableAttributeOptions($product, $request->selected_attribute_options ?: []));
        } catch (\Exception|\Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }
    }
}
