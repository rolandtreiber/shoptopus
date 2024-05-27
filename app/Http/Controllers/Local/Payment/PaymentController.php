<?php

namespace App\Http\Controllers\Local\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Local\Payment\ExecuteRequest;
use App\Http\Requests\Local\Payment\GetClientSettingsRequest;
use App\Services\Remote\Payment\PaymentServiceInterface;

class PaymentController extends Controller
{
    private PaymentServiceInterface $paymentService;

    public function __construct(PaymentServiceInterface $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Pay the order
     */
    public function execute(ExecuteRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            return response()->json(
                $this->getResponse([], $this->paymentService->executePayment($request->validated()), $request)
            );
        } catch (\Exception|\Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }
    }

    /**
     * Get a single payment provider settings
     */
    public function getClientSettings(GetClientSettingsRequest $request, string $provider): \Illuminate\Http\JsonResponse
    {
        try {
            return response()->json(
                $this->getResponse([], $this->paymentService->getClientSettings($provider, $request->validated()['orderId']), $request)
            );
        } catch (\Exception|\Error $e) {
            return $this->errorResponse($e, __('error_messages.'.$e->getCode()));
        }
    }
}
