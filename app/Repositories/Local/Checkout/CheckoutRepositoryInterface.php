<?php

namespace App\Repositories\Local\Checkout;

use App\Models\Cart;

interface CheckoutRepositoryInterface
{
    public function createPendingOrderFromCart(array $payload): array;

    public function revertOrder(array $payload): array;

    public function getAvailableDeliveryTypesForAddress(array $payload): array;

    public function checkAvailabilities(Cart $cart): array;

}
