<?php

namespace App\Repositories\Local\ProductCategory;

use App\Models\ProductCategory;
use Illuminate\Support\Facades\DB;
use App\Repositories\Local\ModelRepository;
use App\Services\Local\Error\ErrorServiceInterface;

class ProductCategoryRepository extends ModelRepository implements ProductCategoryRepositoryInterface
{
    public function __construct(ErrorServiceInterface $errorService, ProductCategory $model)
    {
        parent::__construct($errorService, $model);
    }

    /**
     * Get the discount rules for the given product categories
     *
     * @param array $productCategoryIds
     * @return array
     * @throws \Exception
     */
    public function getDiscountRules(array $productCategoryIds = []) : array
    {
        try {
            $dynamic_placeholders = trim(str_repeat('?,', count($productCategoryIds)), ',');

            return DB::select("
                SELECT
                    drpc.product_category_id,
                    dr.id,
                    dr.type,
                    dr.name,
                    dr.amount,
                    dr.valid_from,
                    dr.valid_until,
                    dr.slug
                FROM discount_rules AS dr
                JOIN discount_rule_product_category AS drpc ON drpc.discount_rule_id = dr.id
                WHERE drpc.product_category_id IN ($dynamic_placeholders)
                AND dr.valid_from <= DATETIME()
                AND dr.valid_until >= DATETIME()
                AND dr.deleted_at IS NULL
                AND dr.enabled IS TRUE
            ", $productCategoryIds);
        } catch (\Exception | \Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * Get the products for the given product categories
     *
     * @param array $productCategoryIds
     * @return array
     * @throws \Exception
     */
    public function getProductIds(array $productCategoryIds = []) : array
    {
        try {
            $dynamic_placeholders = trim(str_repeat('?,', count($productCategoryIds)), ',');

            return DB::select("
                SELECT
                    ppc.product_category_id,
                    p.id
                FROM products AS p
                JOIN product_product_category AS ppc ON ppc.product_id = p.id
                WHERE ppc.product_category_id IN ($dynamic_placeholders)
                AND p.deleted_at IS NULL
            ", $productCategoryIds);
        } catch (\Exception | \Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * Get the subcategories
     *
     * @param array $productCategoryIds
     * @return array
     * @throws \Exception
     */
    public function getSubcategories(array $productCategoryIds = []) : array
    {
        try {
            $dynamic_placeholders = trim(str_repeat('?,', count($productCategoryIds)), ',');

            $columns = implode(',', $this->getSelectableColumns(false));

            return DB::select("
                SELECT
                    $columns
                FROM product_categories AS pc
                WHERE pc.parent_id IN ($dynamic_placeholders)
                AND pc.enabled IS TRUE
                AND pc.deleted_at IS NULL
            ", $productCategoryIds);
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
        $ids = collect($result)->pluck('id')->toArray();

        $discount_rules = [];
        $products = [];
        $subcategories = [];

        if (!in_array('discount_rules', $excludeRelationships)) {
            $discount_rules = $this->getDiscountRules($ids);
        }

        if (!in_array('products', $excludeRelationships)) {
            $products = $this->getProductIds($ids);
        }

        if (!in_array('subcategories', $excludeRelationships)) {
            $subcategories = $this->getSubcategories($ids);
        }

        try {
            foreach ($result as &$model) {
                $modelId = $model['id'];

                $model['discount_rules'] = [];
                $model['product_ids'] = [];
                $model['subcategories'] = [];

                foreach ($discount_rules as $discount_rule) {
                    if ($discount_rule['product_category_id'] === $modelId) {
                        unset($discount_rule['product_category_id']);
                        array_push($model['discount_rules'], $discount_rule);
                    }
                }

                foreach ($products as $product) {
                    if ($product['product_category_id'] === $modelId
                        && !in_array($product['id'], array_column($model['product_ids'], 'id'))
                    ) {
                        array_push($model['product_ids'], $product['id']);
                    }
                }

                foreach ($subcategories as $subcategory) {
                    if ($subcategory['parent_id'] === $modelId) {
                        array_push($model['subcategories'], $subcategory);
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
            "{$this->model_table}.slug",
            "{$this->model_table}.description",
            "{$this->model_table}.menu_image",
            "{$this->model_table}.header_image",
            "{$this->model_table}.parent_id"
        ];

        return $withTableNamePrefix
            ? $columns
            : array_map(function($column_name){
                return str_replace($this->model_table . '.', '', $column_name);
            }, $columns);
    }
}
