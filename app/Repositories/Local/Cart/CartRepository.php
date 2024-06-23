<?php

namespace App\Repositories\Local\Cart;

use App\Http\Resources\Public\Product\CartProductResource;
use App\Models\Cart;
use App\Models\CartProduct;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Repositories\Local\ModelRepository;
use App\Services\Local\Error\ErrorServiceInterface;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class CartRepository extends ModelRepository implements CartRepositoryInterface
{
    public function __construct(ErrorServiceInterface $errorService, Cart $model)
    {
        parent::__construct($errorService, $model);
    }


    /**
     * Add item to cart.
     */
    public function addItem(array $payload): array
    {
        if (!$payload['quantity']) {
            $payload['quantity'] = 1;
        }
        try {
            $cart = $payload['cart_id']
                ? $this->get($payload['cart_id'])
                : $this->post([]);

            $cart_product_table = DB::table('cart_product');
            $cart_product_item = $cart_product_table
                ->where('cart_id', $cart['id'])
                ->where('product_id', $payload['product_id']);
            if ($payload['product_variant_id']) {
                $cart_product_item = $cart_product_item->where('product_variant_id', $payload['product_variant_id']);
            } else {
                $cart_product_item = $cart_product_item->whereNull('product_variant_id');
            }

            if ($cart_product_item->exists()) {
                $current_quantity = (int) $cart_product_item->first('quantity')['quantity'];
                if (Product::checkStockAvailability($current_quantity + (int) $payload['quantity'], $payload['product_id'], $payload['product_variant_id'])) {
                    $cart_product_table->update([
                        'quantity' => $current_quantity + (int) $payload['quantity'],
                        'product_variant_id' => $payload['product_variant_id'] ?? null,
                    ]);
                }
            } else {
                $cartProduct = new CartProduct();
                $cartProduct->id = "";
                $cartProduct->cart_id = $cart['id'];
                $cartProduct->product_id = $payload['product_id'];
                $cartProduct->quantity = $payload['quantity'];
                $cartProduct->product_variant_id = $payload['product_variant_id'] ?? null;
                $cartProduct->save();
            }

            return $this->get(value: $cart['id'], excludeRelationships: ['user']);
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * Remove item from cart.
     */
    public function removeItem(array $payload): array
    {
        try {
            $cart = $this->get($payload['cart_id']);

            $cart_product_item = DB::table('cart_product')
                ->where('cart_id', $cart['id'])
                ->where('product_id', $payload['product_id']);

            if ($payload['product_variant_id']) {
                $cart_product_item = $cart_product_item->where('product_variant_id', $payload['product_variant_id']);
            } else {
                $cart_product_item = $cart_product_item->whereNull('product_variant_id');
            }

            $cart_product_item->delete();

            return $this->get(value: $cart['id'], excludeRelationships: ['user']);
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * Remove item from cart.
     */
    public function removeAll(array $payload): array
    {
        try {
            $cart = $this->get($payload['cart_id']);

            DB::table('cart_product')
                ->where('cart_id', $cart['id'])->delete();

            return $this->get(value: $cart['id'], excludeRelationships: ['user']);
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * Update quantity for a given product
     */
    public function updateQuantity(array $payload): array
    {
        try {
            $cart_product_table = DB::table('cart_product');

            $cart_product = $cart_product_table
                ->where('cart_id', $payload['cart_id'])
                ->where('product_id', $payload['product_id']);

            if ($payload['product_variant_id']) {
                $cart_product = $cart_product->where('product_variant_id', $payload['product_variant_id']);
            } else {
                $cart_product = $cart_product->whereNull('product_variant_id');
            }


            if (! $cart_product->exists()) {
                throw new \Exception('Cart or product cannot be found.', Config::get('api_error_codes.services.cart.productNotFound'));
            }

            $cart_product->update(['quantity' => $payload['quantity']]);

            return $this->get(value: $payload['cart_id'], excludeRelationships: ['user']);
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * Merge the user's carts
     */
    public function mergeUserCarts(string $userId, string $cartId): array
    {
        try {
            $cart = $this->getCartForUser($userId);

            $secondary_cart = $this->get($cartId, 'id', ['user']);

            if ($secondary_cart) {
                $secondary_cart_products = $secondary_cart['products'];

                if (! empty($secondary_cart_products)) {
                    if (! $cart) {
                        $cart = $this->post(['user_id' => $userId]);
                    }

                    foreach ($secondary_cart_products as $secondary_cart_product) {
                        $payload = [
                            'product_id' => $secondary_cart_product['id'],
                            'quantity' => $secondary_cart_product['quantity'],
                            'cart_id' => $cart['id'],
                            'product_variant_id' => $secondary_cart_product['product_variant_id'],
                        ];

                        $this->addItem($payload);
                    }
                }

                $this->delete($cartId);
            }

            return $this->getCartForUser($userId);
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * Get the user's cart
     */
    public function getCartForUser(string $userId): array
    {
        try {
            $cart = $this->get($userId, 'user_id', ['user']);

            if (empty($cart)) {
                $this->post(['user_id' => $userId]);

                $cart = $this->get($userId, 'user_id', ['user']);
            }

            return $cart;
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * Get the users for the carts
     *
     *
     * @throws \Exception
     */
    public function getUsers(array $userIds = []): array
    {
        try {
            $dynamic_placeholders = trim(str_repeat('?,', count($userIds)), ',');

            return DB::select("
                SELECT
                    u.id,
                    u.first_name,
                    u.last_name,
                    u.email,
                    u.name,
                    u.initials,
                    u.prefix,
                    u.phone,
                    u.avatar,
                    u.email_verified_at,
                    u.client_ref,
                    u.temporary,
                    u.is_favorite
                FROM users AS u
                WHERE u.id IN ($dynamic_placeholders)
                AND u.deleted_at IS NULL
            ", $userIds);
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * Get the products for the given cart
     */
    public function getProducts(array $cartIds = []): array
    {
        try {
            $dynamic_placeholders = trim(str_repeat('?,', count($cartIds)), ',');

            return DB::select("
                SELECT
                    cp.cart_id,
                    cp.product_variant_id,
                    cp.quantity,
                    p.id,
                    p.name,
                    p.slug,
                    p.subtitle,
                    p.headline,
                    p.short_description,
                    p.description,
                    p.price,
                    p.status,
                    p.purchase_count,
                    p.stock,
                    p.backup_stock,
                    p.sku,
                    p.cover_photo,
                    p.rating
                FROM products AS p
                JOIN cart_product AS cp ON cp.product_id = p.id
                JOIN carts as c ON c.id = cp.cart_id
                WHERE cp.cart_id IN ($dynamic_placeholders)
            ", $cartIds);
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * Get the required related models for the given parent
     *
     *
     * @throws \Exception
     */
    public function getTheResultWithRelationships($result, array $excludeRelationships = []): array
    {
        try {
            $ids = collect($result)->pluck('id')->toArray();

            $users = [];
            $products = [];

            if (! in_array('user', $excludeRelationships)) {
                $users = $this->getUsers(collect($result)->unique('user_id')->pluck('user_id')->toArray());
            }

            if (! in_array('products', $excludeRelationships)) {
                $cart = Cart::find($ids[0]);
                $products = CartProductResource::collection($cart->products);
            }
            foreach ($result as &$model) {
                $modelId = $model['id'];

                $model['user'] = null;
                $model['products'] = [];

                foreach ($users as $user) {
                    if ($user['id'] === $model['user_id']) {
                        $model['user'] = $user;
                    }
                }

                foreach ($products as $product) {
                    array_push($model['products'], $product);
                }
            }

            return $result;
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * Get the columns for selection
     */
    public function getSelectableColumns(bool $withTableNamePrefix = true): array
    {
        $columns = [
            "{$this->model_table}.id",
            "{$this->model_table}.user_id",
            "{$this->model_table}.ip_address",
        ];

        return $withTableNamePrefix
            ? $columns
            : array_map(function ($column_name) {
                return str_replace($this->model_table.'.', '', $column_name);
            }, $columns);
    }
}
