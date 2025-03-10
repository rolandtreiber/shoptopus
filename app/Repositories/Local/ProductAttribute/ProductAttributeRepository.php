<?php

namespace App\Repositories\Local\ProductAttribute;

use App\Enums\ProductAttributeType;
use App\Models\ProductAttribute;
use App\Repositories\Local\ModelRepository;
use App\Services\Local\Error\ErrorServiceInterface;
use Illuminate\Support\Facades\DB;

class ProductAttributeRepository extends ModelRepository implements ProductAttributeRepositoryInterface
{
    public ?string $product_category_id = null;

    public function __construct(ErrorServiceInterface $errorService, ProductAttribute $model)
    {
        parent::__construct($errorService, $model);
    }

    /**
     * Get all models for a specific product category
     */
    public function getAllForProductCategory(string $product_category_id, array $page_formatting = []): array
    {
        try {
            $this->product_category_id = $product_category_id;

            $filter_string = ' JOIN product_product_attribute AS ppa ON ppa.product_attribute_id = product_attributes.id AND ppa.product_attribute_option_id IS NOT NULL';
            $filter_string .= ' JOIN product_product_category AS ppc ON ppc.product_id = ppa.product_id';
            $filter_string .= ' WHERE ppc.product_category_id IN (?)';
            $filter_string .= ' AND product_attributes.enabled IS TRUE';
            $filter_string .= ' AND product_attributes.deleted_at IS NULL';
            $filter_string .= ' GROUP BY product_attributes.id';

            $filter_vars = (object) [
                'filter_string' => $filter_string,
                'query_parameters' => [$product_category_id],
            ];

            $filters = ['custom_filter_vars' => $filter_vars];

            return $this->getAll($page_formatting, $filters);
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * Get the product attribute options for the given product attributes
     */
    public function getOptions(array $productAttributeIds = []): array
    {
        try {
            $dynamic_placeholders = trim(str_repeat('?,', count($productAttributeIds)), ',');

            $query_params = $productAttributeIds;

            $sql = 'SELECT pao.product_attribute_id, pao.id, pao.name, pao.slug, pao.value, pao.image';
            $sql .= ' FROM product_attribute_options AS pao';
            $sql .= ' LEFT JOIN product_product_attribute AS ppa ON ppa.product_attribute_option_id = pao.id AND ppa.product_id IS NOT NULL';
            $sql .= ' LEFT JOIN product_attribute_product_variant AS papv ON papv.product_attribute_option_id = pao.id AND papv.product_variant_id IS NOT NULL';

            if ($this->product_category_id) {
                $sql .= ' JOIN product_product_category AS ppc ON ppc.product_id = ppa.product_id';
            }

            $sql .= " WHERE pao.product_attribute_id IN ($dynamic_placeholders)";

            if ($this->product_category_id) {
                $sql .= ' AND ppc.product_category_id IN (?)';

                array_push($query_params, $this->product_category_id);
            }

            $sql .= ' AND pao.deleted_at IS NULL';
            $sql .= ' AND pao.enabled IS TRUE';
            $sql .= ' GROUP BY pao.id';

            return DB::select($sql, $query_params);
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * Get the products for the given product attributes
     *
     *
     * @throws \Exception
     */
    public function getProductIds(array $productAttributeIds = []): array
    {
        try {
            $dynamic_placeholders = trim(str_repeat('?,', count($productAttributeIds)), ',');

            return DB::select("
                SELECT
                    ppa.product_attribute_id,
                    ppa.product_id
                FROM products AS p
                JOIN product_product_attribute AS ppa ON ppa.product_id = p.id AND ppa.product_attribute_option_id IS NOT NULL
                WHERE ppa.product_attribute_id IN ($dynamic_placeholders)
                AND p.deleted_at IS NULL

            ", $productAttributeIds);
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * Get the products for the given product attributes
     *
     *
     * @throws \Exception
     */
    public function getProductVariantIds(array $productAttributeIds = []): array
    {
        try {
            $dynamic_placeholders = trim(str_repeat('?,', count($productAttributeIds)), ',');
            return DB::select("
                SELECT
                    papv.product_attribute_id,
                    papv.product_variant_id
                FROM product_variants AS pv
                JOIN product_attribute_product_variant AS papv ON papv.product_variant_id = pv.id AND papv.product_attribute_option_id IS NOT NULL
                WHERE papv.product_attribute_id IN ($dynamic_placeholders)
                AND pv.deleted_at IS NULL

            ", $productAttributeIds);
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
        $ids = collect($result)->pluck('id')->toArray();
        $options = [];
        $products = [];
        $productVariants = [];

        if (! in_array('options', $excludeRelationships)) {
            $options = $this->getOptions($ids);
        }

        if (! in_array('products', $excludeRelationships)) {
            $products = $this->getProductIds($ids);
        }

        if (! in_array('products_variants', $excludeRelationships)) {
            $productVariants = $this->getProductVariantIds($ids);
        }

        try {
            foreach ($result as $key => &$model) {
                $modelId = $model['id'];

                $model['type'] = strtolower(ProductAttributeType::fromValue((int) $model['type'])->key);

                $model['options'] = [];
                $model['product_ids'] = [];
                $model['product_variant_ids'] = [];

                foreach ($options as $option) {
                    $option['name'] = json_decode($option['name']);
                    $option['image'] = json_decode($option['image']);
                    if ($option['product_attribute_id'] === $modelId) {
                        unset($option['product_attribute_id']);
                        array_push($model['options'], $option);
                    }
                }

                if (empty($model['options'])) {
                    array_splice($result, $key, 1);

                    continue;
                }

                foreach ($products as $product) {
                    if ($product['product_attribute_id'] === $modelId
                        && ! in_array($product['product_id'], array_column($model['product_ids'], 'id'))
                    ) {
                        array_push($model['product_ids'], $product['product_id']);
                    }
                }

                foreach ($productVariants as $productVariant) {
                    if ($productVariant['product_attribute_id'] === $modelId
                        && ! in_array($productVariant['product_variant_id'], array_column($model['product_variant_ids'], 'id'))
                    ) {
                        array_push($model['product_variant_ids'], $productVariant['product_variant_id']);
                    }
                }

                if (empty($model['product_ids']) && empty($model['product_variant_ids'])) {
                    array_splice($result, $key, 1);
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
            "{$this->model_table}.name",
            "{$this->model_table}.slug",
            "{$this->model_table}.type",
            "{$this->model_table}.image",
        ];

        return $withTableNamePrefix
            ? $columns
            : array_map(function ($column_name) {
                return str_replace($this->model_table.'.', '', $column_name);
            }, $columns);
    }
}
