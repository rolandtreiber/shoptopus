<?php

namespace App\Repositories\Local\Order;

use App\Models\Order;
use App\Repositories\Local\ModelRepository;
use App\Services\Local\Error\ErrorServiceInterface;

class OrderRepository extends ModelRepository implements OrderRepositoryInterface
{
    public function __construct(ErrorServiceInterface $errorService, Order $model)
    {
        parent::__construct($errorService, $model);
    }

//    /**
//     * Get the orders for the given voucher code
//     *
//     * @param array $voucherCodeIds
//     * @return array
//     * @throws \Exception
//     */
//    public function getOrders(array $voucherCodeIds = []) : array
//    {
//        $result = $this->orderService->getAll([], [
//            'voucher_code_id' => implode(',', $voucherCodeIds)
//        ]);
//
//        return !empty($result['data']) ? $result['data'] : [];
//    }

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
//            $ids = collect($result)->pluck('user_id')->toArray();
//
//            foreach ($result as &$model) {
//                $modelId = (int) $model['id'];
//
//                if (!in_array('orders', $excludeRelationships)) {
//                    $model['orders'] = [];
//
//                    dd($this->getOrders($ids));
//
//                    foreach ($this->getOrders($ids) as $order) {
//                        if ((int) $order['voucher_code_id'] === $modelId) {
//                            unset($order['voucher_code_id']);
//                            array_push($model['orders'], $order);
//                        }
//                    }
//                }
//            }

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
            "{$this->model_table}.delivery_type_id",
            "{$this->model_table}.voucher_code_id",
            "{$this->model_table}.address_id",
            "{$this->model_table}.original_price",
            "{$this->model_table}.subtotal",
            "{$this->model_table}.total_price",
            "{$this->model_table}.total_discount",
            "{$this->model_table}.delivery",
            "{$this->model_table}.status",
            "{$this->model_table}.deleted_at"
        ];

        return $withTableNamePrefix
            ? $columns
            : array_map(function($column_name){
                return str_replace($this->model_table . '.', '', $column_name);
            }, $columns);
    }
}
