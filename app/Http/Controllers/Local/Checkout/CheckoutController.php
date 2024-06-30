<?php

namespace App\Http\Controllers\Local\Checkout;

use App\Http\Controllers\Controller;
use App\Http\Requests\Local\Checkout\CreatePendingOrderFromCartRequest;
use App\Http\Requests\Local\Checkout\GetAvailableDeliveryTypesRequest;
use App\Http\Requests\Local\Checkout\RevertOrderRequest;
use App\Services\Local\Checkout\CheckoutServiceInterface;
use App\Services\Local\Order\OrderServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    private CheckoutServiceInterface $checkoutService;

    public function __construct(CheckoutServiceInterface $checkoutService)
    {
        $this->checkoutService = $checkoutService;
    }

    public function createPendingOrderFromCart(CreatePendingOrderFromCartRequest $request): JsonResponse
    {
        try {
            return response()->json($this->getResponse([], $this->checkoutService->createPendingOrderFromCart($request->validated()), $request));
        } catch (\Exception|\Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }
    }

    public function revertOrder(RevertOrderRequest $request): JsonResponse
    {
        try {
            return response()->json($this->getResponse([], $this->checkoutService->revertOrder($request->validated()), $request));
        } catch (\Exception|\Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }
    }

    public function getAvailableDeliveryTypes(GetAvailableDeliveryTypesRequest $request): JsonResponse
    {
        try {
            return response()->json($this->getResponse([], $this->checkoutService->getAvailableDeliveryTypesForAddress($request->validated()), $request));
        } catch (\Exception|\Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }
    }

}
