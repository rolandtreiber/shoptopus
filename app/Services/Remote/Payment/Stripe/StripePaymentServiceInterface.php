<?php

namespace App\Services\Remote\Payment\Stripe;

interface StripePaymentServiceInterface
{
    /**
     * Get the settings for a payment provider
     */
    public function getClientSettings(string $orderId): array;

    /**
     * Execute payment
     */
    public function executePayment(string $orderId, array $provider_payload): array;

    /**
     * Format payment response
     */
    public function formatPaymentResponse(array $executed_payment_response): array;
}
