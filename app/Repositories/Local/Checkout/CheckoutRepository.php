<?php

namespace App\Repositories\Local\Checkout;

use App\Enums\DiscountType;
use App\Enums\OrderStatus;
use App\Exceptions\CheckoutException;
use App\Models\Address;
use App\Models\Cart;
use App\Models\CartProduct;
use App\Models\DeliveryRule;
use App\Models\DeliveryType;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\VoucherCode;
use Illuminate\Support\Facades\DB;

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
        $cart = Cart::find($payload['cart_id']);
        if (!$cart || (count($cart->products) === 0 && array_key_exists('order_id', $payload) === false)) {
            throw new CheckoutException("Empty cart");
        }

        $order = null;
        if (array_key_exists('order_id', $payload)) {
            $order = Order::find($payload['order_id']);
            if (!$order || $order->status !== OrderStatus::AwaitingPayment) {
                throw new CheckoutException("Invalid order. Either not found or not valid order status.");
            }
        }

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
            DB::commit();
            // Let's create and save the address
            $address = new Address();
            $address->fill($payload['address']);
            $address->user_id = $user->id;
            $address->save();
        } elseif (auth()->user() !== null && array_key_exists('address_id', $payload)) {
            $user = auth()->user();
            $address = Address::find($payload['address_id']);
        } else {
            throw new CheckoutException("Checkout error: " . $this->getDetailedValidationErrorMessageOnNestedArrayFields($payload));
        }
        $deliveryType = DeliveryType::find($payload['delivery_type_id']);

        // @phpstan-ignore-next-line
        if (!$user || !$address || !$cart || !$deliveryType) {
            throw new CheckoutException("Checkout error");
        }

        // Create order
        if (!$order) {
            $order = new Order();
        }
        $order->status = OrderStatus::AwaitingPayment;
        $order->user_id = $user->id;
        $order->address_id = $address->id;
        $order->delivery_type_id = $deliveryType->id;

        // Apply voucher code if present
        if (array_key_exists('voucher_code', $payload)) {
            $voucherCode = VoucherCode::where("code", $payload['voucher_code'])->first();
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
                /** @var ProductVariant|null $productVariant */
                $productVariant = ProductVariant::find($productVariantId);
                if ($productVariant !== null) {
                    $productVariant->stock = $productVariant->stock - $cartProduct->pivot->quantity;
                    $productVariant->save();
                } else {
                    throw new CheckoutException("Product variant not found");
                }
            } elseif ($productId) {
                $product = Product::find($productId);
                if ($product) {
                    $product->stock = $product->stock - $cartProduct->pivot->quantity;
                    $product->save();
                } else {
                    throw new CheckoutException("Product not found");
                }
            } else {
                throw new CheckoutException("Checkout error");
            }
        }

        // Delete products from cart
        $cart->products()->detach();

        return [
            'order_id' => $order->id,
            'user_id' => $user->id,
            'cart_id' => $cart->id
        ];

    }

    /**
     * @throws CheckoutException
     */
    public function revertOrder(array $payload): array
    {
        /** @var Order|null $order */
        $order = Order::find($payload['order_id']);
        if (auth()->user()) {
            $user = auth()->user();
        } elseif (array_key_exists('user_id', $payload)) {
            $user = User::where('id', $payload['user_id'])->first();
        } else {
            throw new CheckoutException('User not found');
        }

        $cart = Cart::where('user_id', $user->id)->first();
        $unavailableProducts = [];

        if ($order && $user) {
            if (!$cart) {
                $cart = new Cart();
                $cart->user_id = $user->id;
                $cart->save();
            }
            /** @var OrderProduct $orderProduct */
            foreach ($order->products as $orderProduct) {
                /** Check if the product or/and variant is still available? Why? If website hase high traffic it can happen that we sale-out something even in the sort time window.
                 * If no: don't try to put it back to cart! Signal front-end with missing item and reason.
                 * If yes: Need to check if the amount we try to put back is still available?
                 *    - if no: we not try to put it back to the cart as not enough stock, and signal front-end with missing item and reason.
                 *    - if yes: put it back to the cart
                 */
                $isProductAvailable = true;
                $productVariant = null;
                $product = null;

                // Check if it's a product variant
                if ($orderProduct->pivot->product_variant_id) {
                    $productVariant = ProductVariant::find($orderProduct->pivot->product_variant_id);

                    if (!$productVariant || $productVariant->stock < $orderProduct->pivot->amount) {
                        $isProductAvailable = false;
                        $unavailableProducts[] = [
                            'product_id' => $orderProduct->pivot->product_id,
                            'variant_id' => $orderProduct->pivot->product_variant_id,
                            'name' => json_decode($orderProduct->pivot->name, true),
                            'reason' => 'Not enough stock for variant'
                        ];
                    }
                } else {
                    // Otherwise, it's a standalone product
                    $product = Product::find($orderProduct->pivot->product_id);

                    if (!$product || $product->stock < $orderProduct->pivot->amount) {
                        $isProductAvailable = false;
                        $unavailableProducts[] = [
                            'product_id' => $orderProduct->pivot->product_id,
                            'name' => json_decode($orderProduct->pivot->name, true),
                            'reason' => 'Not enough stock for product'
                        ];
                    }
                }

                // Add to cart if product or variant is available
                if ($isProductAvailable) {
                    $cartProduct = new CartProduct();
                    $cartProduct->cart_id = $cart->id;
                    $cartProduct->product_id = $orderProduct->pivot->product_id;
                    $cartProduct->product_variant_id = $orderProduct->pivot->product_variant_id;
                    $cartProduct->quantity = $orderProduct->pivot->amount;
                    $cartProduct->save();
                    if ($orderProduct->pivot->product_variant_id) {
                        $productVariant = ProductVariant::find($orderProduct->pivot->product_variant_id);
                        $productVariant->stock = $productVariant->stock + $orderProduct->pivot->amount;
                        $productVariant->save();
                    } else {
                        $product = Product::find($orderProduct->pivot->product_id);
                        $product->stock = $product->stock + $orderProduct->pivot->amount;
                        $product->save();
                    }
                }

                // remove the order_product
                $order->products()->detach();
            }
            $order->original_price = 0;
            $order->subtotal = 0;
            $order->total_price = 0;
            $order->total_discount = 0;
            $order->delivery_cost = 0;
            $order->voucher_code_id = null;
            $order->delivery_type_id = null;
            $order->status = OrderStatus::Cancelled;
            $order->save();
            if ($user->temporary) {
                $address = Address::find($order->address_id);
                if ($address) {
                    $address->delete();
                }
            }
        }
        return [
            'user_id' => $user->id,
            'cart_id' => $cart->id,
            'order_id' => $order->id,
            'unavailable_products' => $unavailableProducts
        ];
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
        } elseif (is_array($payload['address'])) {
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


    public function checkAvailabilities(Cart $cart): array
    {
        $report = [
            'status' => 'OK',
            'products_to_review' => []
        ];
        /** @var CartProduct $cartProduct */
        foreach ($cart->products as $cartProduct) {
            if ($cartProduct->product_variant_id !== null) {
                $availableStock = ProductVariant::find($cartProduct->product_variant_id)->stock;
                $name = $cartProduct->productVariant->name;
            } else {
                $availableStock = $cartProduct->stock;
                $name = $cartProduct->name;
            }
            if ($cartProduct->pivot->quantity > $availableStock) {
                $report['products_to_review'][] = [
                    'name' => $name,
                    'available' => $availableStock,
                    'requested' => $cartProduct->pivot->quantity,
                    'product_id' => $cartProduct->pivot->product_id,
                    'product_variant_id' => $cartProduct->pivot->product_variant_id
                ];
            }
            if (count($report['products_to_review']) > 0) {
                $report['status'] = 'REVIEW';
            }
        }
        return $report;
    }

    public function applyVoucherCode(Cart $cart, string $code): array
    {
        $report = [
            'status' => 'OK',
        ];

        /** @var VoucherCode|null $voucherCode */
        $voucherCode = VoucherCode::where("code", $code)->view('enabled')->view('active')->first();
        if (!$voucherCode) {
            $report['status'] = 'INVALID';
            $report['voucher_code_details'] = null;
        } else {
            $type = "";
            switch ($voucherCode->type) {
                case DiscountType::Percentage:
                    $type = "Percentage";
                    break;
                case DiscountType::Amount:
                    $type = "Fix Amount";
            }

            $report['voucher_code_details'] = [
                'type' => $type,
                'value' => $voucherCode->amount,
                'cart_totals' => $cart->getTotals($voucherCode)
            ];
        }
        return $report;
    }

}
