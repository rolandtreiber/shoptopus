<?php

namespace App\Repositories\Local\User;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Local\ModelRepository;
use App\Services\Local\Error\ErrorServiceInterface;
use App\Repositories\Local\Product\ProductRepositoryInterface;

class UserRepository extends ModelRepository implements UserRepositoryInterface
{
    private ProductRepositoryInterface $productRepository;

    public function __construct(ErrorServiceInterface $errorService, User $model, ProductRepositoryInterface $productRepository)
    {
        parent::__construct($errorService, $model);

        $this->productRepository = $productRepository;
    }

    /**
     * Get the currently authenticated user instance
     *
     * @param bool $returnAsArray
     * @return mixed
     */
    public function getCurrentUser(bool $returnAsArray = true) : mixed
    {
        try {
            $user = Auth::check() ? Auth::user() : null;

            if (!$user) {
                return null;
            }

            return $returnAsArray ? $user->toArray() : $user;
        } catch (\Exception | \Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * Get the currently authenticated user's favorited products
     *
     * @return array
     * @throws \Exception
     */
    public function favorites() : array
    {
        try {
            return $this->productRepository->getAll([], [
                'id' => implode(',', $this->getFavoritedProductIds())
            ]);
        } catch (\Exception | \Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * Get the currently authenticated user's favorited product ids
     *
     * @return array
     * @throws \Exception
     */
    public function getFavoritedProductIds() : array
    {
        try {
            $userId = Auth::id();

            if (!$userId) {
                return [];
            }

            $data = DB::select("SELECT fp.product_id FROM favorited_products AS fp WHERE fp.user_id = (?)", [$userId]);

            return !empty($data) ? collect($data)->pluck('product_id')->toArray() : [];
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
            "{$this->model_table}.name",
            "{$this->model_table}.first_name",
            "{$this->model_table}.last_name",
            "{$this->model_table}.email",
            "{$this->model_table}.phone",
            "{$this->model_table}.email_verified_at",
            "{$this->model_table}.avatar",
            "{$this->model_table}.client_ref",
            "{$this->model_table}.client_ref",
            "{$this->model_table}.deleted_at"
        ];

        return $withTableNamePrefix
            ? $columns
            : array_map(function($column_name){
                return str_replace($this->model_table . '.', '', $column_name);
            }, $columns);
    }
}
