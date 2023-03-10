<?php

namespace App\Http\Controllers\Cart;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\AddItemToCartRequest;
use App\Http\Requests\Cart\PatchRequest;
use App\Http\Requests\Cart\RemoveItemFromCartRequest;
use App\Http\Requests\Cart\UpdateQuantityRequest;
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
     *
     * @param  PatchRequest  $request
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(PatchRequest $request, string $id): \Illuminate\Http\JsonResponse
    {
        try {
            $data = $this->cartService->update($id, $request->validated());

            return response()->json($this->putResponse($data));
        } catch (\Exception | \Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }
    }

    /**
     * Update quantity for a given product
     *
     * @param  UpdateQuantityRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateQuantity(UpdateQuantityRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $data = $this->cartService->updateQuantity($request->validated());

            return response()->json($this->putResponse($data));
        } catch (\Exception | \Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }
    }

    /**
     * Add item to cart.
     *
     * @param  AddItemToCartRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addItem(AddItemToCartRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $data = $this->cartService->addItem($request->validated());

            return response()->json($this->postResponse($data));
        } catch (\Exception | \Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }
    }

    /**
     * Remove item from cart.
     *
     * @param  RemoveItemFromCartRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeItem(RemoveItemFromCartRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $this->cartService->removeItem($request->validated());

            return response()->json($this->deleteResponse());
        } catch (\Exception | \Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }
    }
}
