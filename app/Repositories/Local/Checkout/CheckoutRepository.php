<?php

namespace App\Repositories\Local\Checkout;

use App\Repositories\Local\Checkout\CheckoutRepositoryInterface;

class CheckoutRepository implements CheckoutRepositoryInterface
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
