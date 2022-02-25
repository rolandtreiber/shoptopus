<?php

namespace App\Repositories\Local\DeliveryType;

use App\Models\DeliveryType;
use App\Repositories\Local\ModelRepository;
use App\Services\Local\Error\ErrorServiceInterface;

class DeliveryTypeRepository extends ModelRepository implements DeliveryTypeRepositoryInterface
{
    public function __construct(ErrorServiceInterface $errorService, DeliveryType $model)
    {
        parent::__construct($errorService, $model);
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
//            $ids = collect($result)->pluck('id')->toArray();
//
//            foreach ($result as &$model) {
//                $modelId = (int) $model['id'];
//
//                if (!in_array('orders', $excludeRelationships)) {
//                    $model['orders'] = [];
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
            "{$this->model_table}.name",
            "{$this->model_table}.description",
            "{$this->model_table}.price",
            "{$this->model_table}.enabled",
            "{$this->model_table}.enabled_by_default_on_creation",
            "{$this->model_table}.deleted_at"
        ];

        return $withTableNamePrefix
            ? $columns
            : array_map(function($column_name){
                return str_replace($this->model_table . '.', '', $column_name);
            }, $columns);
    }
}
