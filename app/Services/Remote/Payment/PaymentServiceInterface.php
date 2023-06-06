<?php

namespace App\Services\Remote\Payment;

interface PaymentServiceInterface
{
    /**
     * Get the settings for a payment provider
     *
     *
     * @throws \Exception
     */
    public function getClientSettings(string $provider, string $orderId): array;

    /**
     * Execute a payment using the correct gateway
     *
     *
     * @throws \Exception
     */
    public function executePayment(array $payload): array;

    /**
     * Return a uniform response object from the API
     *
     *
     * @throws \Exception
     */
    public function formatPaymentResponse(string $provider, array $executed_payment_response): array;
}
