<?php

namespace App\Http\Controllers\Local\Cart;

use App\Enums\UserInteractionType;
use App\Events\UserInteraction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Local\Cart\AddItemToCartRequest;
use App\Http\Requests\Local\Cart\PatchRequest;
use App\Http\Requests\Local\Cart\RemoveAllItemsFromCartRequest;
use App\Http\Requests\Local\Cart\RemoveItemFromCartRequest;
use App\Http\Requests\Local\Cart\UpdateQuantityRequest;
use App\Http\Resources\Public\Product\CartProductResource;
use App\Models\Cart;
use App\Models\Product;
use App\Services\Local\Cart\CartServiceInterface;
use Illuminate\Http\JsonResponse;

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
    public function show(Cart $cart): JsonResponse
    {
        try {
            return response()->json(
                [
                    'id' => $cart->id,
                    'products' => CartProductResource::collection($cart->products),
                    'totals' => $cart->getTotals(null)
                ]
            );
        } catch (\Exception|\Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }
    }

    /**
     * Update a model
     */
    public function update(PatchRequest $request, string $id): JsonResponse
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
    public function updateQuantity(Cart $cart, UpdateQuantityRequest $request): JsonResponse
    {
        $payload = array_merge($request->validated(), ["cart_id" => $cart->id]);
        try {
            $data = $this->cartService->updateQuantity($payload);

            return response()->json($this->putResponse($data));
        } catch (\Exception|\Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }
    }

    /**
     * Add item to cart.
     */
    public function addItem(AddItemToCartRequest $request): JsonResponse
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
    public function removeItem(RemoveItemFromCartRequest $request): JsonResponse
    {
        try {
            $this->cartService->removeItem($request->validated());
            event(new UserInteraction(UserInteractionType::RemovedItemFromCart, Product::class, $request->product_id));

            return response()->json($this->deleteResponse());
        } catch (\Exception|\Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }
    }

    /**
     * Remove item from cart.
     */
    public function removeAll(RemoveAllItemsFromCartRequest $request): JsonResponse
    {
        try {
            $this->cartService->removeAll($request->validated());
            event(new UserInteraction(UserInteractionType::EmptiedCart, Cart::class, $request->cart_id));

            return response()->json($this->deleteResponse());
        } catch (\Exception|\Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }
    }

}
