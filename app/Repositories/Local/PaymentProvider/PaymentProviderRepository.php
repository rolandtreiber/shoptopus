<?php

namespace App\Repositories\Local\PaymentProvider;

use App\Models\PaymentProvider\PaymentProvider;
use App\Repositories\Local\ModelRepository;
use App\Services\Local\Error\ErrorServiceInterface;
use Illuminate\Support\Facades\DB;

class PaymentProviderRepository extends ModelRepository implements PaymentProviderRepositoryInterface
{
    public function __construct(ErrorServiceInterface $errorService, PaymentProvider $model)
    {
        parent::__construct($errorService, $model);
    }

    /**
     * Get the configs for the given payment provider
     *
     * @param  array  $paymentProviderIds
     * @return array
     */
    public function getConfigs(array $paymentProviderIds = []): array
    {
        $ids = implode(',', $paymentProviderIds);

        return DB::select("
            SELECT
                ppc.payment_provider_id,
                ppc.setting,
                ppc.value,
                ppc.test_value,
                ppc.public
            FROM payment_provider_configs as ppc
            JOIN payment_providers as pp ON pp.id = ppc.payment_provider_id
            WHERE pp.id IN ($ids)
        ");
    }

    /**
     * Get the columns for selection
     *
     * @param  bool  $withTableNamePrefix
     * @return array
     */
    public function getSelectableColumns(bool $withTableNamePrefix = true): array
    {
        $columns = [
            "{$this->model_table}.id",
            "{$this->model_table}.name",
            "{$this->model_table}.enabled",
            "{$this->model_table}.test_mode",
        ];

        return $withTableNamePrefix
            ? $columns
            : array_map(function ($column_name) {
                return str_replace($this->model_table.'.', '', $column_name);
            }, $columns);
    }

    /**
     * Get the required related models for the given payment provider
     *
     * @param $result
     * @param  array  $excludeRelationships
     * @return array
     *
     * @throws \Exception
     */
    public function getTheResultWithRelationships($result, array $excludeRelationships = []): array
    {
        try {
            if (! in_array('payment_provider_configs', $excludeRelationships)) {
                $configs = $this->getConfigs(collect($result)->pluck('id')->toArray());

                foreach ($result as &$model) {
                    $modelId = (int) $model['id'];

                    $model['payment_provider_configs'] = [];
                    foreach ($configs as $config) {
                        if ((int) $config['payment_provider_id'] === $modelId) {
                            unset($config['payment_provider_id']);
                            array_push($model['payment_provider_configs'], $config);
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
}
