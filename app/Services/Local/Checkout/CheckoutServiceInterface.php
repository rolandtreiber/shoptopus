<?php

namespace App\Services\Local\Checkout;

use App\Models\Cart;

interface CheckoutServiceInterface
{
    public function createPendingOrderFromCart(array $payload): array;

    public function revertOrder(array $payload): array;

    public function getAvailableDeliveryTypesForAddress(array $payload): array;

    public function checkAvailabilities(Cart $cart): array;

    public function applyVoucherCode(Cart $cart, string $code): array;

}
