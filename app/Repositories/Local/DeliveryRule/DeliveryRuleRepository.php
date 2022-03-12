<?php

namespace App\Repositories\Local\DeliveryRule;

use App\Models\DeliveryRule;
use Illuminate\Support\Facades\DB;
use App\Repositories\Local\ModelRepository;
use App\Services\Local\Error\ErrorServiceInterface;

class DeliveryRuleRepository extends ModelRepository implements DeliveryRuleRepositoryInterface
{
    public function __construct(ErrorServiceInterface $errorService, DeliveryRule $model)
    {
        parent::__construct($errorService, $model);
    }

    /**
     * Get the delivery types for the given delivery rules
     *
     * @param array $deliveryTypeIds
     * @return array
     * @throws \Exception
     */
    public function getDeliveryTypes(array $deliveryTypeIds = []) : array
    {
        try {
            return DB::select("
                SELECT
                    dt.id,
                    dt.name,
                    dt.description,
                    dt.price,
                    dt.enabled,
                    dt.enabled_by_default_on_creation
                FROM delivery_types AS dt
                WHERE dt.id IN (?)
                AND dt.deleted_at IS NULL
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
            foreach ($result as &$model) {
                $model['delivery_type'] = null;

                if (!in_array('delivery_type', $excludeRelationships)) {
                    $deliveryTypes = $this->getDeliveryTypes(collect($result)->unique('delivery_type_id')->pluck('delivery_type_id')->toArray());

                    foreach ($deliveryTypes as $deliveryType) {
                        if($deliveryType['id'] === $model['delivery_type_id']) {
                            $model['delivery_type'] = $deliveryType;
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
            "{$this->model_table}.delivery_type_id",
            "{$this->model_table}.postcodes",
            "{$this->model_table}.min_weight",
            "{$this->model_table}.max_weight",
            "{$this->model_table}.min_distance",
            "{$this->model_table}.max_distance",
            "{$this->model_table}.distance_unit",
            "{$this->model_table}.lat",
            "{$this->model_table}.lon",
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
