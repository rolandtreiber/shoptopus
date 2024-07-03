<?php

namespace App\Repositories\Local\Checkout;

use App\Enums\OrderStatus;
use App\Exceptions\CheckoutException;
use App\Helpers\GeneralHelper;
use App\Models\Address;
use App\Models\Cart;
use App\Models\DeliveryRule;
use App\Models\DeliveryType;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\VoucherCode;
use App\Repositories\Local\Checkout\CheckoutRepositoryInterface;

class CheckoutRepository implements CheckoutRepositoryInterface
{
    private function getDetailedValidationErrorMessageOnNestedArrayFields($payload): string
    {
        $guestCheckout = $payload['guest_checkout'] === true || $payload['guest_checkout'] === "1";
        if ($guestCheckout === true && !array_key_exists('user', $payload)) return "No user details present at guest checkout";
        if ($guestCheckout === true && !is_array($payload['user'])) return "Invalid user data";
        if ($guestCheckout === true && !array_key_exists('email', $payload['user'])) return "No email field present in the user object";
        if ($guestCheckout === true && !array_key_exists('first_name', $payload['user'])) return "No first_name field present in the user object";
        if ($guestCheckout === true && !array_key_exists('last_name', $payload['user'])) return "No last_name field present in the user object";
        if ($guestCheckout === true && (
            !str_contains($payload['user']['email'], '@')
            || !str_contains($payload['user']['email'], '.')
            || strlen($payload['user']['email']) < 4
            )) return "Invalid user email";
        if ($guestCheckout === true && !array_key_exists('address', $payload)) return "No address details present at guest checkout";
        if ($guestCheckout === true && !is_array($payload['address'])) return "Invalid address data";
        if ($guestCheckout === true && !array_key_exists('town', $payload['address'])) return "No town field present in the address object";
        if ($guestCheckout === true && !array_key_exists('post_code', $payload['address'])) return "No post_code field present in the address object";
        if ($guestCheckout === true && !array_key_exists('address_line_1', $payload['address'])) return "No address_line_1 field present in the address object";
        if ($guestCheckout === true && !array_key_exists('lat', $payload['address'])) return "No lat field present in the address object";
        if ($guestCheckout === true && !array_key_exists('lon', $payload['address'])) return "No lon field present in the address object";

        return "Generic error";
    }

    /**
     * @throws CheckoutException
     */
    public function createPendingOrderFromCart(array $payload): array
    {
        $user = null;
        $address = null;
        // Guest
        if (($payload['guest_checkout'] === true || $payload['guest_checkout'] === "1")
            && array_key_exists('user', $payload)
            && is_array($payload['user'])
            && array_key_exists('email', $payload['user'])
            && array_key_exists('first_name', $payload['user'])
            && array_key_exists('last_name', $payload['user'])
            && str_contains($payload['user']['email'], '@')
            && str_contains($payload['user']['email'], '.')
            && strlen($payload['user']['email']) > 3
            && array_key_exists('address', $payload)
            && is_array($payload['address'])
            && array_key_exists('town', $payload['address'])
            && array_key_exists('post_code', $payload['address'])
            && array_key_exists('address_line_1', $payload['address'])
            && array_key_exists('lat', $payload['address'])
            && array_key_exists('lon', $payload['address'])
        ) {
            // Let's create the user
            $user = new User();
            $user->email = $payload['user']['email'];
            $user->first_name = $payload['user']['first_name'];
            $user->last_name = $payload['user']['last_name'];
            $user->temporary = true;
            $user->save();

            // Let's create and save the address
            $address = new Address();
            $address->fill($payload['address']);
            $address->user_id = $user->id;
            $address->save();
        } elseif(auth()->user() !== null && array_key_exists('address_id', $payload)) {
            $user = auth()->user();
            $address = Address::find($payload['address_id']);
        } else {
            throw new CheckoutException("Checkout error: " . $this->getDetailedValidationErrorMessageOnNestedArrayFields($payload));
        }

        $cart = Cart::find($payload['cart_id']);
        $deliveryType = DeliveryType::find($payload['delivery_type_id']);

        if (!$user || !$address || !$cart || !$deliveryType) {
            throw new CheckoutException("Checkout error");
        }

        // Create order
        $order = new Order();
        $order->status = OrderStatus::AwaitingPayment;
        $order->user_id = $user->id;
        $order->address_id = $address->id;
        $order->delivery_type_id = $deliveryType->id;

        // Apply voucher code if present
        if (array_key_exists('voucher_code_id', $payload)) {
            $voucherCode = VoucherCode::find($payload['voucher_code_id']);
            if ($voucherCode && $voucherCode->status === 1) {
                $order->voucher_code_id = $voucherCode->id;
            }
        }

        $order->save();

        foreach ($cart->products as $cartProduct) {
            $orderProduct = new OrderProduct();
            $orderProduct->order_id = $order->id;
            $orderProduct->product_id = $cartProduct->pivot->product_id;
            $orderProduct->product_variant_id = $cartProduct->pivot->product_variant_id;
            $orderProduct->amount = $cartProduct->pivot->quantity;
            $orderProduct->save();
        }

        // Updating the stock levels
        foreach ($cart->products as $cartProduct) {
            $productVariantId = $cartProduct->pivot->product_variant_id;
            $productId = $cartProduct->pivot->product_id;
            if ($productVariantId) {
                /** @var ProductVariant $productVariant */
                $productVariant = ProductVariant::find($productVariantId);
                if ($productVariant) {
                    $productVariant->stock = $productVariant->stock - $cartProduct->pivot->quantity;
                } else {
                    throw new CheckoutException("Product variant not found");
                }
            } elseif($productId) {
                $product = Product::find($productId);
                if ($product) {
                    $product->stock = $product->stock - $cartProduct->pivot->quantity;
                } else {
                    throw new CheckoutException("Product not found");
                }
            } else {
                throw new CheckoutException("Checkout error");
            }
        }

        // Delete products from cart
        foreach ($cart->products as $cartProduct) {
            $cartProduct->delete();
        }

        return [
            'order_id' => $order->id
        ];
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
