<?php

namespace App\Repositories\Local\DeliveryType;

use App\Models\DeliveryType;
use Illuminate\Support\Facades\DB;
use App\Repositories\Local\ModelRepository;
use App\Services\Local\Error\ErrorServiceInterface;

class DeliveryTypeRepository extends ModelRepository implements DeliveryTypeRepositoryInterface
{
    public function __construct(ErrorServiceInterface $errorService, DeliveryType $model)
    {
        parent::__construct($errorService, $model);
    }

    /**
     * Get the delivery rules for the given delivery types
     *
     * @param array $deliveryTypeIds
     * @return array
     * @throws \Exception
     */

    public function getDeliveryRules(array $deliveryTypeIds = []) : array
    {
        try {
            return DB::select("
                SELECT
                    dr.id,
                    dr.delivery_type_id,
                    dr.postcodes,
                    dr.min_weight,
                    dr.max_weight,
                    dr.min_distance,
                    dr.max_distance,
                    dr.distance_unit,
                    dr.lat,
                    dr.lon
                FROM delivery_rules AS dr
                JOIN delivery_types AS dt ON dt.id IN (?)
                WHERE dr.deleted_at IS NULL
                AND dr.enabled IS TRUE
            ", [implode(',', $deliveryTypeIds)]);
        } catch (\Exception | \Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * Get the orders for the given delivery types
     *
     * @param array $deliveryTypeIds
     * @return array
     * @throws \Exception
     */
    public function getOrders(array $deliveryTypeIds = []) : array
    {
        try {
            return DB::select("
                SELECT
                    o.id,
                    o.delivery_type_id,
                    o.user_id,
                    o.voucher_code_id,
                    o.address_id,
                    o.original_price,
                    o.subtotal,
                    o.total_price,
                    o.total_discount,
                    o.delivery_cost,
                    o.status
                FROM orders AS o
                JOIN delivery_types AS dt ON dt.id IN (?)
                WHERE o.deleted_at IS NULL
            ", [implode(',', $deliveryTypeIds)]);
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

            $deliveryRules = [];
            $orders = [];

            if (!in_array('delivery_rules', $excludeRelationships)) {
                $deliveryRules = $this->getDeliveryRules($ids);
            }

            if (!in_array('orders', $excludeRelationships)) {
                $orders = $this->getOrders($ids);
            }

            foreach ($result as &$model) {
                $modelId = (int) $model['id'];

                $model['delivery_rules'] = [];
                $model['orders'] = [];

                foreach ($deliveryRules as $deliveryRule) {
                    if ((int) $deliveryRule['delivery_type_id'] === $modelId) {
                        unset($deliveryRule['delivery_type_id']);
                        array_push($model['delivery_rules'], $deliveryRule);
                    }
                }

                foreach ($orders as $order) {
                    if ((int) $order['delivery_type_id'] === $modelId) {
                        unset($order['delivery_type_id']);
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
