<?php

namespace App\Repositories\Local\Checkout;

use App\Exceptions\CheckoutException;
use App\Models\Address;
use App\Models\Cart;
use App\Models\DeliveryRule;
use App\Models\DeliveryType;
use App\Models\Order;
use App\Repositories\Local\Checkout\CheckoutRepositoryInterface;

class CheckoutRepository implements CheckoutRepositoryInterface
{

    public function createPendingOrderFromCart(array $payload): array
    {
        return [];
    }

    public function revertOrder(array $payload): array
    {
        return [];
    }

    /**
     * @throws CheckoutException
     */
    public function getAvailableDeliveryTypesForAddress(array $payload): array
    {
        $result = [];
        $address = null;
        if ($payload['address_id']) {
            $address = Address::find($payload['address_id']);
        } elseif(is_array($payload['address'])) {
            if (array_key_exists('town', $payload['address'])
                && array_key_exists('post_code', $payload['address'])
                && array_key_exists('address_line_1', $payload['address'])
                && array_key_exists('lat', $payload['address'])
                && array_key_exists('lon', $payload['address'])
            ) {
                $address = new Address();
                $address->lat = $payload['address']['lat'];
                $address->lon = $payload['address']['lon'];
                $address->post_code = $payload['address']['post_code'];
            } else {
                throw new CheckoutException('Missing address fields');
            }
        }
        /** @var Cart $cart */
        $cart = Cart::find($payload['cart_id']);
        if ($cart == null) {
            throw new CheckoutException('Cart not found');
        }
        if (count($cart->products) === 0) {
            throw new CheckoutException('No products found in cart');
        }
        $totalWeight = $cart->total_weight;
        $products = $cart->products;

        $deliveryTypes = DeliveryType::where('enabled', 1)->get();

        /** @var DeliveryType $deliveryType */
        foreach ($deliveryTypes as $deliveryType) {
            $rules = $deliveryType->deliveryRules;
            $eligible = true;
            /** @var DeliveryRule $rule */
            foreach ($rules as $rule) {
                $distance = $rule->getDistanceFromAddress($address);
                if ($rule->lat !== null
                    && $rule->lon !== null
                ) {
                    if ($rule->max_distance !== null && $distance > $rule->max_distance) {
                        $eligible = false;
                    }
                    if ($rule->min_distance !== null && $distance < $rule->min_distance) {
                        $eligible = false;
                    }
                }
                if ($rule->max_weight !== null && $totalWeight > $rule->max_weight) {
                    $eligible = false;
                }
                if ($rule->min_weight !== null && $totalWeight < $rule->min_weight) {
                    $eligible = false;
                }
                if (is_array($rule->postcodes) && count($rule->postcodes) > 0) {
                    $postcodeAppears = false;
                    foreach ($rule->postcodes as $postcode) {
                        if (trim(strtolower(str_replace(' ', '', $postcode))) === trim(strtolower(str_replace(' ', '', $address->post_code)))) {
                            $postcodeAppears = true;
                        }
                    }
                    $eligible = $postcodeAppears;
                }
            }
            if ($eligible === true) {
                $dt = [
                    'id' => $deliveryType->id,
                    'slug' => $deliveryType->slug,
                    'name' => $deliveryType->getTranslations('name'),
                    'description' => $deliveryType->getTranslations('description'),
                    'price' => $deliveryType->price,
                ];
                $result[] = $dt;
            }
        }
        return $result;
    }
}
