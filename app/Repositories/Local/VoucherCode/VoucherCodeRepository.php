<?php

namespace App\Repositories\Local\VoucherCode;

use App\Models\VoucherCode;
use App\Repositories\Local\ModelRepository;
use App\Services\Local\Error\ErrorServiceInterface;
use Illuminate\Support\Facades\DB;

class VoucherCodeRepository extends ModelRepository implements VoucherCodeRepositoryInterface
{
    public function __construct(ErrorServiceInterface $errorService, VoucherCode $model)
    {
        parent::__construct($errorService, $model);
    }

    /**
     * Get the orders for the given voucher codes
     *
     *
     * @throws \Exception
     */
    public function getOrders(array $voucherCodeIds = []): array
    {
        try {
            $dynamic_placeholders = trim(str_repeat('?,', count($voucherCodeIds)), ',');

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
                WHERE o.voucher_code_id IN ($dynamic_placeholders)
                AND o.deleted_at IS NULL
            ", $voucherCodeIds);
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

            $orders = [];

            if (! in_array('orders', $excludeRelationships)) {
                $orders = $this->getOrders($ids);
            }

            foreach ($result as &$model) {
                $modelId = $model['id'];

                $model['orders'] = [];

                foreach ($orders as $order) {
                    if ($order['voucher_code_id'] === $modelId) {
                        unset($order['voucher_code_id']);
                        array_push($model['orders'], $order);
                    }
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
            "{$this->model_table}.type",
            "{$this->model_table}.amount",
            "{$this->model_table}.code",
            "{$this->model_table}.valid_from",
            "{$this->model_table}.valid_until",
            "{$this->model_table}.enabled",
            "{$this->model_table}.deleted_at",
        ];

        return $withTableNamePrefix
            ? $columns
            : array_map(function ($column_name) {
                return str_replace($this->model_table.'.', '', $column_name);
            }, $columns);
    }
}
