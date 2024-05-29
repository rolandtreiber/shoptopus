<?php

namespace App\Repositories\Local\ProductTag;

use App\Models\ProductTag;
use App\Repositories\Local\ModelRepository;
use App\Services\Local\Error\ErrorServiceInterface;
use Illuminate\Support\Facades\DB;

class ProductTagRepository extends ModelRepository implements ProductTagRepositoryInterface
{
    public function __construct(ErrorServiceInterface $errorService, ProductTag $model)
    {
        parent::__construct($errorService, $model);
    }

    /**
     * Get the products count for products
     *
     *
     * @throws \Exception
     */
    public function getProductsCountForTags(array $productTagIds = []): array
    {
        try {
            $dynamic_placeholders = trim(str_repeat('?,', count($productTagIds)), ',');

            return DB::select("
                SELECT ppt.product_tag_id as product_tag_id,
                       COUNT(p.id) AS product_count
                FROM products p
                                   JOIN product_product_tag ppt ON ppt.product_id = p.id
                                   WHERE ppt.product_tag_id IN ($dynamic_placeholders)
                GROUP BY ppt.product_tag_id;                
            ", $productTagIds);
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    public function getTheResultWithRelationships($result, array $excludeRelationships = []): array
    {
        $ids = collect($result)->pluck('id')->toArray();

        $products_counts = $this->getProductsCountForTags($ids);

        try {
            foreach ($result as &$model) {
                $modelId = $model['id'];

                $model['discount_rules'] = [];
                $model['product_ids'] = [];
                $model['subcategories'] = [];

                foreach ($products_counts as $products_count) {
                    if ($products_count['product_tag_id'] === $modelId) {
                        $model['products_count'] = $products_count['product_count'];
                    }
                }

                $model['badge'] = json_decode($model['badge']);
            }

            return $result;
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    public function getSelectableColumns(bool $withTableNamePrefix = true): array
    {
        $columns = [
            "{$this->model_table}.id",
            "{$this->model_table}.name",
            "{$this->model_table}.slug",
            "{$this->model_table}.description",
            "{$this->model_table}.badge",
            "{$this->model_table}.display_badge",
            "{$this->model_table}.enabled",
        ];

        return $withTableNamePrefix
            ? $columns
            : array_map(function ($column_name) {
                return str_replace($this->model_table.'.', '', $column_name);
            }, $columns);
    }

}