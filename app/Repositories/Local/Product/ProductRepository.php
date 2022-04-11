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
                JOIN discount_rule_product AS drp ON drp.discount_rule_id = dr.id
                WHERE drp.product_id IN (?)
                AND dr.deleted_at IS NULL
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
                JOIN product_product_category AS ppc ON ppc.product_category_id = pc.id
                WHERE ppc.product_id IN (?)
                AND pc.deleted_at IS NULL
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
                JOIN product_product_tag AS ppt ON ppt.product_tag_id = pt.id
                WHERE ppt.product_id IN (?)
                AND pt.deleted_at IS NULL
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
     * Get the product attributes for the given products
     *
     * @param array $productIds
     * @return array
     * @throws \Exception
     */
    public function getProductAttributes(array $productIds = []) : array
    {
        try {
            return DB::select("
                SELECT
                    ppa.product_id,
                    ppa.product_attribute_id,
                    ppa.product_attribute_option_id,
                    pa.id,
                    pa.name,
                    pa.slug,
                    pa.type,
                    pa.image,
                    pao.id as option_id,
                    pao.name as option_name,
                    pao.slug as option_slug,
                    pao.value as option_value,
                    pao.image as option_image,
                    pao.enabled as option_enabled,
                    pao.deleted_at as option_deleted_at,
                    pao.product_attribute_id as option_id
                FROM product_attributes AS pa
                JOIN product_product_attribute AS ppa ON ppa.product_attribute_id = pa.id
                JOIN product_attribute_options AS pao ON pao.product_attribute_id = pa.id
                WHERE ppa.product_id IN (?)
                AND pa.deleted_at IS NULL
                AND pa.enabled IS TRUE
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
        $product_attributes = [];

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

        if (!in_array('product_attributes', $excludeRelationships)) {
            $product_attributes = $this->getProductAttributes($ids);
        }

        try {
            foreach ($result as &$model) {
                $modelId = $model['id'];

                $model['discount_rules'] = [];
                $model['product_categories'] = [];
                $model['product_tags'] = [];
                $model['product_variants'] = [];
                $model['product_attributes'] = [];

                foreach ($discount_rules as $discount_rule) {
                    if ($discount_rule['product_id'] === $modelId) {
                        unset($discount_rule['product_id']);
                        array_push($model['discount_rules'], $discount_rule);
                    }
                }

                foreach ($product_categories as $product_category) {
                    if ($product_category['product_id'] === $modelId) {
                        unset($product_category['product_id']);
                        array_push($model['product_categories'], $product_category);
                    }
                }

                foreach ($product_tags as $product_tag) {
                    if ($product_tag['product_id'] === $modelId) {
                        unset($product_tag['product_id']);
                        array_push($model['product_tags'], $product_tag);
                    }
                }

                foreach ($product_variants as $product_variant) {
                    if ($product_variant['product_id'] === $modelId) {
                        unset($product_variant['product_id']);
                        array_push($model['product_variants'], $product_variant);
                    }
                }

                foreach ($product_attributes as $product_attribute) {
                    if ($product_attribute['product_id'] === $modelId) {
                        unset($product_attribute['product_id']);

                        $attribute_exists = in_array(
                            $product_attribute['product_attribute_id'],
                            array_column($model['product_attributes'], 'id')
                        );

                        $options_data = [
                            'id' => $product_attribute['option_id'],
                            'name' => $product_attribute['option_name'],
                            'slug' => $product_attribute['option_slug'],
                            'option_value' => $product_attribute['option_value'],
                            'image' => $product_attribute['option_image'],
                            'option_enabled' => (bool) $product_attribute['option_enabled'],
                            'option_deleted_at' => $product_attribute['option_deleted_at']
                        ];

                        if (!$attribute_exists) {
                            $attribute_data = [
                                'id' => $product_attribute['id'],
                                'name' => $product_attribute['name'],
                                'slug' => $product_attribute['slug'],
                                'type' => $product_attribute['type'],
                                'image' => $product_attribute['image'],
                                'options' => []
                            ];

                            if ($options_data['option_enabled'] && is_null($options_data['option_deleted_at'])) {
                                unset($options_data['option_enabled']);
                                unset($options_data['option_deleted_at']);

                                array_push($attribute_data['options'], $options_data);
                            }

                            array_push($model['product_attributes'], $attribute_data);
                        } else {
                            if ($options_data['option_enabled'] && is_null($options_data['option_deleted_at'])) {
                                foreach ($model['product_attributes'] as &$attribute) {
                                    if ($attribute['id'] === $product_attribute['product_attribute_id']) {
                                        unset($options_data['option_enabled']);
                                        unset($options_data['option_deleted_at']);

                                        array_push($attribute['options'], $options_data);

                                        break;
                                    }
                                }
                            }
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
