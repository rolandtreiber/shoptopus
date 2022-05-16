<?php

namespace App\Services\Remote\Payment\Amazon;

interface AmazonPaymentServiceInterface {

    /**
     * Get the settings for a payment provider
     *
     * @param string $orderId
     * @return array
     * @throws \Exception
     */
    public function getClientSettings(string $orderId) : array;

    /**
     * Execute payment
     *
     * @param array $order
     * @param array $provider_payload
     * @return array
     */
    public function executePayment(array $order, array $provider_payload) : array;

    /**
     * Format payment response
     *
     * @param array $executed_payment_response
     * @return array
     */
    public function formatPaymentResponse(array $executed_payment_response) : array;

}
