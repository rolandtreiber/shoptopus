<?php

namespace App\Repositories\Local\DeliveryRule;

use App\Models\DeliveryRule;
use App\Repositories\Local\ModelRepository;
use App\Services\Local\Error\ErrorServiceInterface;
use Illuminate\Support\Facades\DB;

class DeliveryRuleRepository extends ModelRepository implements DeliveryRuleRepositoryInterface
{
    public function __construct(ErrorServiceInterface $errorService, DeliveryRule $model)
    {
        parent::__construct($errorService, $model);
    }

    /**
     * Get the deliveryTypes for the given delivery types
     *
     * @param array $deliveryRuleIds
     * @return array
     * @throws \Exception
     */
    public function getDeliveryTypes(array $deliveryRuleIds = []) : array
    {
        $ids = implode(',', $deliveryRuleIds);

        return DB::select("
            SELECT
                dr.delivery_type_id,
                dt.id,
                dt.name,
                dt.description,
                dt.price,
                dt.enabled,
                dt.enabled_by_default_on_creation
            FROM delivery_rules as dr
            JOIN delivery_types as dt ON dt.id = dr.delivery_type_id
            WHERE dr.id IN (?)
        ", [$ids]);
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

                if (!in_array('delivery_type', $excludeRelationships)) {
                    foreach ($this->getDeliveryTypes($ids) as $deliveryType) {
                        if ((int) $deliveryType['delivery_type_id'] === $modelId) {
                            unset($deliveryType['delivery_type_id']);
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
