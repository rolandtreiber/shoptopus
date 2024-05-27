<?php

namespace App\Http\Controllers\Local\Cart;

use App\Enums\UserInteractionType;
use App\Events\UserInteraction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Local\Cart\AddItemToCartRequest;
use App\Http\Requests\Local\Cart\PatchRequest;
use App\Http\Requests\Local\Cart\RemoveItemFromCartRequest;
use App\Http\Requests\Local\Cart\UpdateQuantityRequest;
use App\Models\Product;
use App\Services\Local\Cart\CartServiceInterface;

class CartController extends Controller
{
    private CartServiceInterface $cartService;

    public function __construct(CartServiceInterface $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Update a model
     */
    public function update(PatchRequest $request, string $id): \Illuminate\Http\JsonResponse
    {
        try {
            $data = $this->cartService->update($id, $request->validated());

            return response()->json($this->putResponse($data));
        } catch (\Exception|\Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }
    }

    /**
     * Update quantity for a given product
     */
    public function updateQuantity(UpdateQuantityRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $data = $this->cartService->updateQuantity($request->validated());

            return response()->json($this->putResponse($data));
        } catch (\Exception|\Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }
    }

    /**
     * Add item to cart.
     */
    public function addItem(AddItemToCartRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $data = $this->cartService->addItem($request->validated());
            event(new UserInteraction(UserInteractionType::AddedItemToCart, Product::class, $request->product_id));

            return response()->json($this->postResponse($data));
        } catch (\Exception|\Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }
    }

    /**
     * Remove item from cart.
     */
    public function removeItem(RemoveItemFromCartRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $this->cartService->removeItem($request->validated());
            event(new UserInteraction(UserInteractionType::RemovedItemFromCart, Product::class, $request->product_id));

            return response()->json($this->deleteResponse());
        } catch (\Exception|\Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }
    }
}
