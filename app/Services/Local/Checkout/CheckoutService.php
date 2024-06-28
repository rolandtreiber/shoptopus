<?php

namespace App\Services\Local\Checkout;

use App\Services\Local\Checkout\CheckoutServiceInterface;

class CheckoutService implements CheckoutServiceInterface
{

    public function createPendingOrderFromCart(array $payload): array
    {
        // TODO: Implement createPendingOrderFromCart() method.
    }

    public function revertOrder(array $payload): array
    {
        // TODO: Implement revertOrder() method.
    }
}
