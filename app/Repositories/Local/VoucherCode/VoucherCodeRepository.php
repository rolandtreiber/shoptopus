<?php

namespace App\Repositories\Local\VoucherCode;

use App\Models\VoucherCode;
use Illuminate\Support\Facades\DB;
use App\Repositories\Local\ModelRepository;
use App\Services\Local\Error\ErrorServiceInterface;

class VoucherCodeRepository extends ModelRepository implements VoucherCodeRepositoryInterface
{
    public function __construct(ErrorServiceInterface $errorService, VoucherCode $model)
    {
        parent::__construct($errorService, $model);
    }

    /**
     * Get the orders for the given voucher codes
     *
     * @param array $voucherCodeIds
     * @return array
     * @throws \Exception
     */
    public function getOrders(array $voucherCodeIds = []) : array
    {
        try {
            return DB::select("
                SELECT
                    o.id,
                    o.user_id,
                    o.delivery_type_id,
                    o.voucher_code_id,
                    o.address_id,
                    o.original_price,
                    o.subtotal,
                    o.total_price,
                    o.total_discount,
                    o.delivery_cost,
                    o.status
                FROM orders AS o
                JOIN voucher_codes AS vc ON vc.id IN (?)
                WHERE o.deleted_at IS NULL
            ", [implode(',', $voucherCodeIds)]);
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

            $orders = [];

            if (!in_array('orders', $excludeRelationships)) {
                $orders = $this->getOrders($ids);
            }

            foreach ($result as &$model) {
                $modelId = (int) $model['id'];

                $model['orders'] = [];

                foreach ($orders as $order) {
                    if ((int) $order['voucher_code_id'] === $modelId) {
                        unset($order['voucher_code_id']);
                        array_push($model['orders'], $order);
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
            "{$this->model_table}.type",
            "{$this->model_table}.amount",
            "{$this->model_table}.code",
            "{$this->model_table}.valid_from",
            "{$this->model_table}.valid_until",
            "{$this->model_table}.enabled",
            "{$this->model_table}.deleted_at"
        ];

        return $withTableNamePrefix
            ? $columns
            : array_map(function($column_name){
                return str_replace($this->model_table . '.', '', $column_name);
            }, $columns);
    }
}
