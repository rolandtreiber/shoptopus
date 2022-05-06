<?php

namespace App\Repositories\Local\ProductAttribute;

use App\Models\ProductAttribute;
use Illuminate\Support\Facades\DB;
use App\Enums\ProductAttributeType;
use App\Repositories\Local\ModelRepository;
use App\Services\Local\Error\ErrorServiceInterface;

class ProductAttributeRepository extends ModelRepository implements ProductAttributeRepositoryInterface
{
    public function __construct(ErrorServiceInterface $errorService, ProductAttribute $model)
    {
        parent::__construct($errorService, $model);
    }

    /**
     * Get all models for a specific product category
     *
     * @param string $product_category_id
     * @param array $page_formatting
     * @return array
     */
    public function getAllForProductCategory(string $product_category_id, array $page_formatting = []) : array
    {
        try {
            $filter_string = " JOIN product_product_attribute AS ppa ON ppa.product_attribute_id = product_attributes.id";
            $filter_string .= " JOIN product_product_category AS ppc ON ppc.product_id = ppa.product_id";
            $filter_string .= " WHERE ppc.product_category_id IN (?)";
            $filter_string .= " AND product_attributes.enabled IS TRUE";
            $filter_string .= " AND product_attributes.deleted_at IS NULL";
            $filter_string .= " GROUP BY product_attributes.id";

            $filter_vars = (object) [
                'filter_string' => $filter_string,
                'query_parameters' => [$product_category_id]
            ];

            $filters = ['custom_filter_vars' => $filter_vars];

            return $this->getAll($page_formatting, $filters);
        } catch (\Exception | \Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * Get the product attribute options for the given product attributes
     *
     * @param array $productAttributeIds
     * @return array
     * @throws \Exception
     */
    public function getOptions(array $productAttributeIds = []) : array
    {
        try {
            $dynamic_placeholders = trim(str_repeat('?,', count($productAttributeIds)), ',');

            return DB::select("
                SELECT
                    pao.product_attribute_id,
                    pao.id,
                    pao.name,
                    pao.slug,
                    pao.value,
                    pao.image
                FROM product_attribute_options AS pao
                JOIN product_product_attribute AS ppa ON ppa.product_attribute_option_id = pao.id AND ppa.product_id IS NOT NULL
                WHERE pao.product_attribute_id IN ($dynamic_placeholders)
                AND pao.deleted_at IS NULL
                AND pao.enabled IS TRUE
                GROUP BY pao.id
            ", $productAttributeIds);
        } catch (\Exception | \Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * Get the products for the given product attributes
     *
     * @param array $productAttributeIds
     * @return array
     * @throws \Exception
     */
    public function getProductIds(array $productAttributeIds = []) : array
    {
        try {
            $dynamic_placeholders = trim(str_repeat('?,', count($productAttributeIds)), ',');

            return DB::select("
                SELECT
                    ppa.product_attribute_id,
                    ppa.product_id
                FROM products AS p
                JOIN product_product_attribute AS ppa ON ppa.product_id = p.id
                WHERE ppa.product_attribute_id IN ($dynamic_placeholders)
                AND p.deleted_at IS NULL
            ", $productAttributeIds);
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

        $options = [];
        $products = [];

        if (!in_array('options', $excludeRelationships)) {
            $options = $this->getOptions($ids);
        }

        if (!in_array('products', $excludeRelationships)) {
            $products = $this->getProductIds($ids);
        }

        try {
            foreach ($result as $key => &$model) {
                $modelId = $model['id'];

                $model['type'] = strtolower(ProductAttributeType::fromValue((int) $model['type'])->key);

                $model['options'] = [];
                $model['product_ids'] = [];

                foreach ($options as $option) {
                    if ($option['product_attribute_id'] === $modelId) {
                        unset($option['product_attribute_id']);
                        array_push($model['options'], $option);
                    }
                }

                if(empty($model['options'])) {
                    array_splice($result, $key, 1);

                    continue;
                }

                foreach ($products as $product) {
                    if ($product['product_attribute_id'] === $modelId) {
                        unset($product['product_attribute_id']);
                        array_push($model['product_ids'], $product['product_id']);
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
            "{$this->model_table}.type",
            "{$this->model_table}.image"
        ];

        return $withTableNamePrefix
            ? $columns
            : array_map(function($column_name){
                return str_replace($this->model_table . '.', '', $column_name);
            }, $columns);
    }
}
