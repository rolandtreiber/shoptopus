<?php

namespace App\Repositories\Local\Cart;

use App\Models\Cart;
use Illuminate\Support\Facades\DB;
use App\Repositories\Local\ModelRepository;
use App\Services\Local\Error\ErrorServiceInterface;

class CartRepository extends ModelRepository implements CartRepositoryInterface
{
    public function __construct(ErrorServiceInterface $errorService, Cart $model)
    {
        parent::__construct($errorService, $model);
    }

    /**
     * Get the users for the carts
     *
     * @param array $userIds
     * @return array
     * @throws \Exception
     */
    public function getUsers(array $userIds = []) : array
    {
        try {
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
                WHERE u.id IN (?)
                AND u.deleted_at IS NULL
            ", [implode(',', $userIds)]);
        } catch (\Exception | \Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * Get the products for the given cart
     *
     * @param array $cartIds
     * @return array
     */
    public function getProducts(array $cartIds = []) : array
    {
        try {
            return [];
            //return !empty($result['data']) ? $result['data'] : [];
        } catch (\Exception | \Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * Get the required related models for the given parent
     *
     * @param $result
     * @param array $excludeRelationships
     * @return array
     * @throws \Exception
     */
    public function getTheResultWithRelationships($result, array $excludeRelationships = []) : array
    {
        try {
            $ids = collect($result)->pluck('id')->toArray();

            foreach ($result as &$model) {
                $modelId = (int) $model['id'];

                $model['user'] = null;
                $model['products'] = [];

                if (!in_array('user', $excludeRelationships)) {
                    $users = $this->getUsers(collect($result)->unique('user_id')->pluck('user_id')->toArray());

                    foreach ($users as $user) {
                        if ($user['id'] === $model['user_id']) {
                            $model['user'] = $user;
                        }
                    }
                }

                if (!in_array('products', $excludeRelationships)) {
                    foreach ($this->getProducts($ids) as $product) {
                        if ((int) $product['cart_id'] === $modelId) {
                            unset($product['cart_id']);
                            array_push($model['products'], $product);
                        }
                    }
                }
            }

            return $result;
        } catch (\Exception | \Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * Get the columns for selection
     *
     * @param bool $withTableNamePrefix
     * @return array
     */
    public function getSelectableColumns(bool $withTableNamePrefix = true) : array
    {
        $columns = [
            "{$this->model_table}.id",
            "{$this->model_table}.user_id",
            "{$this->model_table}.ip_address",
            "{$this->model_table}.deleted_at"
        ];

        return $withTableNamePrefix
            ? $columns
            : array_map(function($column_name){
                return str_replace($this->model_table . '.', '', $column_name);
            }, $columns);
    }
}
