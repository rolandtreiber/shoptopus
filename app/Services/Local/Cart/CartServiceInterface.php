<?php

namespace App\Services\Local\Cart;

interface CartServiceInterface {

    /**
     * Get the user's cart
     *
     * @param string $userId
     * @return array
     */
    public function getCartForUser(string $userId) : array;

}
