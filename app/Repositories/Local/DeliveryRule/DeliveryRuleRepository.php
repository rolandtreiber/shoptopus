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
     * Get the delivery types for the given delivery rules
     *
     *
     * @throws \Exception
     */
    public function getDeliveryTypes(array $deliveryTypeIds = []): array
    {
        try {
            $dynamic_placeholders = trim(str_repeat('?,', count($deliveryTypeIds)), ',');

            return DB::select("
                SELECT
                    dt.id,
                    dt.name,
                    dt.description,
                    dt.price
                FROM delivery_types AS dt
                WHERE dt.id IN ($dynamic_placeholders)
                AND dt.deleted_at IS NULL
                AND dt.enabled IS TRUE
            ", $deliveryTypeIds);
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
            $delivery_types = [];

            if (! in_array('delivery_type', $excludeRelationships)) {
                $delivery_types = $this->getDeliveryTypes(collect($result)
                    ->unique('delivery_type_id')
                    ->pluck('delivery_type_id')
                    ->toArray()
                );
            }

            foreach ($result as &$model) {
                $model['delivery_type'] = null;

                foreach ($delivery_types as $deliveryType) {
                    if ($deliveryType['id'] === $model['delivery_type_id']) {
                        $model['delivery_type'] = $deliveryType;
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
            "{$this->model_table}.deleted_at",
        ];

        return $withTableNamePrefix
            ? $columns
            : array_map(function ($column_name) {
                return str_replace($this->model_table.'.', '', $column_name);
            }, $columns);
    }
}
