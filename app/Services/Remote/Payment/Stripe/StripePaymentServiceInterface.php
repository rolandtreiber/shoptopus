<?php

namespace App\Services\Remote\Payment\Stripe;

use App\Models\Cart;
use App\Models\DeliveryType;
use App\Models\VoucherCode;

interface StripePaymentServiceInterface
{
    /**
     * Get the settings for a payment provider
     */
    public function getClientSettings(array $totals, Cart $cart, DeliveryType $deliveryType, VoucherCode|null $voucherCode): array;

    /**
     * Execute payment
     */
    public function executePayment(string $userId, string $orderId, array $provider_payload): array;

    /**
     * Format payment response
     */
    public function formatPaymentResponse(array $executed_payment_response): array;
}
