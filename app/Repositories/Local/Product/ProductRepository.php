<?php

namespace App\Repositories\Local\Product;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Repositories\Local\ModelRepository;
use App\Services\Local\Error\ErrorServiceInterface;

class ProductRepository extends ModelRepository implements ProductRepositoryInterface
{
    public function __construct(ErrorServiceInterface $errorService, Product $model)
    {
        parent::__construct($errorService, $model);
    }

    /**
     * Get the discount rules for the given products
     *
     * @param array $productIds
     * @return array
     * @throws \Exception
     */
    public function getDiscountRules(array $productIds = []) : array
    {
        try {
            return DB::select("
                SELECT
                    drp.product_id,
                    dr.id,
                    dr.type,
                    dr.name,
                    dr.amount,
                    dr.valid_from,
                    dr.valid_until,
                    dr.slug
                FROM discount_rules AS dr
                JOIN discount_rule_product AS drp ON drp.product_id IN (?)
                WHERE dr.deleted_at IS NULL
                AND dr.enabled = true
            ", [implode(',', $productIds)]);
        } catch (\Exception | \Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * Get the product categories for the given products
     *
     * @param array $productIds
     * @return array
     * @throws \Exception
     */
    public function getProductCategories(array $productIds = []) : array
    {
        try {
            return DB::select("
                SELECT
                    ppc.product_id,
                    pc.id,
                    pc.name,
                    pc.slug,
                    pc.parent_id,
                    pc.description,
                    pc.menu_image,
                    pc.header_image
                FROM product_categories AS pc
                JOIN product_product_category AS ppc ON ppc.product_id IN (?)
                WHERE pc.deleted_at IS NULL
                AND pc.enabled IS TRUE
            ", [implode(',', $productIds)]);
        } catch (\Exception | \Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * Get the product tags for the given products
     *
     * @param array $productIds
     * @return array
     * @throws \Exception
     */
    public function getProductTags(array $productIds = []) : array
    {
        try {
            return DB::select("
                SELECT
                    ppt.product_id,
                    pt.id,
                    pt.name,
                    pt.slug,
                    pt.description,
                    pt.badge,
                    pt.display_badge
                FROM product_tags AS pt
                JOIN product_product_tag AS ppt ON ppt.product_id IN (?)
                WHERE pt.deleted_at IS NULL
                AND pt.enabled IS TRUE
            ", [implode(',', $productIds)]);
        } catch (\Exception | \Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * Get the product variants
     *
     * @param array $productIds
     * @return array
     * @throws \Exception
     */
    public function getProductVariants(array $productIds = []) : array
    {
        try {
            return DB::select("
                SELECT
                    pv.product_id,
                    pv.id,
                    pv.slug,
                    pv.price,
                    pv.data,
                    pv.stock,
                    pv.sku,
                    pv.description
                FROM product_variants AS pv
                WHERE pv.product_id IN (?)
                AND pv.deleted_at IS NULL
                AND pv.enabled IS TRUE
            ", [implode(',', $productIds)]);
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
        $product_categories = [];
        $product_tags = [];
        $product_variants = [];

        if (!in_array('discount_rules', $excludeRelationships)) {
            $discount_rules = $this->getDiscountRules($ids);
        }

        if (!in_array('product_categories', $excludeRelationships)) {
            $product_categories = $this->getProductCategories($ids);
        }

        if (!in_array('product_tags', $excludeRelationships)) {
            $product_tags = $this->getProductTags($ids);
        }

        if (!in_array('product_variants', $excludeRelationships)) {
            $product_variants = $this->getProductVariants($ids);
        }

        try {
            foreach ($result as &$model) {
                $modelId = (int) $model['id'];

                $model['discount_rules'] = [];
                $model['product_categories'] = [];
                $model['product_tags'] = [];
                $model['product_variants'] = [];

                foreach ($discount_rules as $discount_rule) {
                    if ((int) $discount_rule['product_id'] === $modelId) {
                        unset($discount_rule['product_id']);
                        array_push($model['discount_rules'], $discount_rule);
                    }
                }

                foreach ($product_categories as $product_category) {
                    if ((int) $product_category['product_id'] === $modelId) {
                        unset($product_category['product_id']);
                        array_push($model['product_categories'], $product_category);
                    }
                }

                foreach ($product_tags as $product_tag) {
                    if ((int) $product_tag['product_id'] === $modelId) {
                        unset($product_tag['product_id']);
                        array_push($model['product_tags'], $product_tag);
                    }
                }

                foreach ($product_variants as $product_variant) {
                    if ((int) $product_variant['product_id'] === $modelId) {
                        unset($product_variant['product_id']);
                        array_push($model['product_variants'], $product_variant);
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
            "{$this->model_table}.short_description",
            "{$this->model_table}.description",
            "{$this->model_table}.price",
            "{$this->model_table}.status",
            "{$this->model_table}.purchase_count",
            "{$this->model_table}.stock",
            "{$this->model_table}.backup_stock",
            "{$this->model_table}.sku",
            "{$this->model_table}.cover_photo"
        ];

        return $withTableNamePrefix
            ? $columns
            : array_map(function($column_name){
                return str_replace($this->model_table . '.', '', $column_name);
            }, $columns);
    }
}
