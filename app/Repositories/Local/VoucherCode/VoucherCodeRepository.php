<?php

namespace App\Repositories\Local\VoucherCode;

use App\Models\VoucherCode;
use App\Repositories\Local\ModelRepository;
use App\Services\Local\Error\ErrorServiceInterface;
use App\Services\Local\Order\OrderServiceInterface;

class VoucherCodeRepository extends ModelRepository implements VoucherCodeRepositoryInterface
{
    private $orderService;

    public function __construct(ErrorServiceInterface $errorService, VoucherCode $model, OrderServiceInterface $orderService)
    {
        parent::__construct($errorService, $model);

        $this->orderService = $orderService;
    }

    /**
     * Get the orders for the given voucher code
     *
     * @param array $voucherCodeIds
     * @return array
     * @throws \Exception
     */
    public function getOrders(array $voucherCodeIds = []) : array
    {
        $result = $this->orderService->getAll([], [
            'voucher_code_id' => implode(',', $voucherCodeIds)
        ]);

        return !empty($result['data']) ? $result['data'] : [];
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

                if (!in_array('orders', $excludeRelationships)) {
                    $model['orders'] = [];

                    foreach ($this->getOrders($ids) as $order) {
                        if ((int) $order['voucher_code_id'] === $modelId) {
                            unset($order['voucher_code_id']);
                            array_push($model['orders'], $order);
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
