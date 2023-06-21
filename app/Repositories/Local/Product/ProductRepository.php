<?php

namespace App\Repositories\Local\Product;

use App\Enums\ProductAttributeType;
use App\Enums\UserInteractionType;
use App\Events\UserInteraction;
use App\Helpers\GeneralHelper;
use App\Models\FileContent;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Repositories\Local\ModelRepository;
use App\Services\Local\Error\ErrorServiceInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductRepository extends ModelRepository implements ProductRepositoryInterface
{
    public function __construct(ErrorServiceInterface $errorService, Product $model)
    {
        parent::__construct($errorService, $model);
    }

    /**
     * Save product to favorites
     *
     *
     * @throws \Exception
     */
    public function favorite(string $productId): array
    {
        try {
            $userId = Auth::id();

            if (! $userId) {
                throw new \Exception('Unauthenticated.');
            }

            $table = DB::table('favorited_products');

            $favorited = $table->where([
                'user_id' => $userId,
                'product_id' => $productId,
            ]);

            if ($favorited->exists()) {
                $favorited->delete();
                event(new UserInteraction(UserInteractionType::UnfavouritedProduct, Product::class, $productId));
            } else {
                $table->insert([
                    'user_id' => $userId,
                    'product_id' => $productId,
                ]);
                event(new UserInteraction(UserInteractionType::FavouritedProduct, Product::class, $productId));
            }

            return ['favorited' => $favorited->exists()];
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * Get the product attributes for the given products
     *
     *
     * @throws \Exception
     */
    public function getProductAttributes(array $productIds = []): array
    {
        try {
            $dynamic_placeholders = trim(str_repeat('?,', count($productIds)), ',');

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
                    pao.product_attribute_id as option_product_attribute_id,
                    pao.name as option_name,
                    pao.slug as option_slug,
                    pao.value as option_value,
                    pao.image as option_image
                FROM product_attributes AS pa
                JOIN product_product_attribute AS ppa ON ppa.product_attribute_id = pa.id AND ppa.product_attribute_option_id IS NOT NULL
                JOIN product_attribute_options AS pao ON pao.product_attribute_id = ppa.product_attribute_id AND pao.enabled IS TRUE AND pao.deleted_at IS NULL
                WHERE ppa.product_id IN ($dynamic_placeholders)
                AND pa.deleted_at IS NULL
                AND pa.enabled IS TRUE
            ", $productIds);
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * Get the discount rules for the given products
     *
     *
     * @throws \Exception
     */
    public function getDiscountRules(array $productIds = []): array
    {
        try {
            $dynamic_placeholders = trim(str_repeat('?,', count($productIds)), ',');

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
                WHERE drp.product_id IN ($dynamic_placeholders)
                AND dr.valid_from <= current_timestamp
                AND dr.valid_until >= current_timestamp
                AND dr.deleted_at IS NULL
                AND dr.enabled IS TRUE
            ", $productIds);
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * Get the product categories for the given products
     *
     *
     * @throws \Exception
     */
    public function getProductCategories(array $productIds = []): array
    {
        try {
            $dynamic_placeholders = trim(str_repeat('?,', count($productIds)), ',');

            return DB::select("
                SELECT
                    ppc.product_id,
                    pc.id,
                    pc.name,
                    pc.slug,
                    pc.parent_id,
                    pc.description,
                    pc.menu_image,
                    pc.header_image,
                    dr.id as discount_rule_id,
                    dr.amount as discount_rule_amount,
                    dr.type as discount_rule_type
                FROM product_categories AS pc
                JOIN product_product_category AS ppc ON ppc.product_category_id = pc.id
                LEFT JOIN discount_rule_product_category AS drpc ON drpc.product_category_id = pc.id
                LEFT JOIN discount_rules AS dr ON dr.id = drpc.discount_rule_id
                                                      AND dr.enabled IS TRUE
                                                      AND dr.deleted_at IS NULL
                                                      AND dr.valid_from <= current_timestamp
                                                      AND dr.valid_until >= current_timestamp
                WHERE ppc.product_id IN ($dynamic_placeholders)
                AND pc.deleted_at IS NULL
                AND pc.enabled IS TRUE
            ", $productIds);
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * Get the product tags for the given products
     *
     *
     * @throws \Exception
     */
    public function getProductTags(array $productIds = []): array
    {
        try {
            $dynamic_placeholders = trim(str_repeat('?,', count($productIds)), ',');

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
                WHERE ppt.product_id IN ($dynamic_placeholders)
                AND pt.deleted_at IS NULL
                AND pt.enabled IS TRUE
            ", $productIds);
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * Get the product variants
     *
     *
     * @throws \Exception
     */
    public function getProductVariants(array $productIds = []): array
    {
        try {
            $dynamic_placeholders = trim(str_repeat('?,', count($productIds)), ',');

            return DB::select("
                SELECT
                    pv.product_id,
                    pv.id,
                    pv.slug,
                    pv.price,
                    pv.data,
                    pv.stock,
                    pv.sku,
                    pv.description,
                    pa.id as product_attribute_id,
                    pa.name as product_attribute_name,
                    pa.slug as product_attribute_slug,
                    pa.type as product_attribute_type,
                    pa.image as product_attribute_image,
                    pao.id as product_attribute_option_id,
                    pao.name as product_attribute_option_name,
                    pao.slug as product_attribute_option_slug,
                    pao.value as product_attribute_option_value,
                    pao.image as product_attribute_option_image
                FROM product_variants AS pv
                LEFT JOIN product_attribute_product_variant AS papv ON papv.product_variant_id = pv.id
                LEFT JOIN product_attributes AS pa ON pa.id = papv.product_attribute_id AND pa.enabled IS TRUE AND pa.deleted_at IS NULL
                LEFT JOIN product_attribute_options AS pao ON pao.product_attribute_id = papv.product_attribute_id AND pao.id = papv.product_attribute_option_id AND pao.enabled IS TRUE AND pao.deleted_at IS NULL
                WHERE pv.product_id IN ($dynamic_placeholders)
                AND pv.deleted_at IS NULL
                AND pv.enabled IS TRUE
            ", $productIds);
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    public function getAvailableAttributeOptions(Product $product, array $selectedAttributeOptionIds = []): array
    {
        $baseQuery = DB::table('product_attribute_product_variant')
            ->join('product_variants as product_variant', 'product_variant.id', '=', 'product_attribute_product_variant.product_variant_id')
            ->where('product_variant.product_id', '=', $product->id);

        $variantAttributeOptionsQuery = $baseQuery
            ->groupBy('product_variant.id')
            ->select([
                'product_variant.id',
                'product_variant.stock',
                'product_attribute_product_variant.product_attribute_id',
                'product_attribute_product_variant.product_attribute_option_id',
                'attribute_options',
            ]);

        foreach ($selectedAttributeOptionIds as $attributeOptionId) {
            $variantAttributeOptionsQuery->where('attribute_options', 'LIKE', '%'.$attributeOptionId.'%');
        }

        $allOptions = $variantAttributeOptionsQuery->pluck('attribute_options')->map(function ($attributeOptions) {
            return json_decode($attributeOptions);
        })->toArray();

        array_walk_recursive($allOptions, function ($a) use (&$return) {
            $return[] = $a;
        });
        $availableAttributeOptionIds = array_values(array_unique($return ?? []));
        $variantIds = $variantAttributeOptionsQuery->pluck('product_variant.id');
        $variantsBaseQuery = DB::table('product_variants')->where('product_id', $product->id)->whereIn('id', $variantIds)->select('price');
        $lowestVariantPrice = $variantsBaseQuery->clone()->orderBy('price')->first();
        $highestVariantPrice = $variantsBaseQuery->clone()->orderByDesc('price')->first();
        $files = FileContent::where('fileable_type', Product::class)
            ->where('fileable_id', $product->id)
            ->get();
        $variantFiles = FileContent::where('fileable_type', ProductVariant::class)
            ->whereIn('fileable_id', $variantIds)
            ->get();

        return [
            'available_attribute_options' => $availableAttributeOptionIds,
            'variant_ids' => $variantIds,
            'lowest_variant_price' => [
                'original' => $product->getFinalPriceAttribute($lowestVariantPrice['price']),
                'discounted' => $product->getFinalPriceAttribute($lowestVariantPrice['price']),
            ],
            'highest_variant_price' => [
                'original' => $product->getFinalPriceAttribute($highestVariantPrice['price']),
                'discounted' => $product->getFinalPriceAttribute($highestVariantPrice['price']),
            ],
            'files' => $files->merge($variantFiles),
            'stock' => DB::table('product_variants')->whereIn('id', $variantIds)->sum('stock'),
        ];
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

        $product_attributes = [];
        $discount_rules = [];
        $product_categories = [];
        $product_tags = [];
        $product_variants = [];

        if (! in_array('product_attributes', $excludeRelationships)) {
            $product_attributes = $this->getProductAttributes($ids);
        }

        if (! in_array('discount_rules', $excludeRelationships)) {
            $discount_rules = $this->getDiscountRules($ids);
        }

        if (! in_array('product_categories', $excludeRelationships)) {
            $product_categories = $this->getProductCategories($ids);
        }

        if (! in_array('product_tags', $excludeRelationships)) {
            $product_tags = $this->getProductTags($ids);
        }

        if (! in_array('product_variants', $excludeRelationships)) {
            $product_variants = $this->getProductVariants($ids);
        }

        try {
            foreach ($result as &$model) {
                $modelId = $model['id'];

                $model['product_attributes'] = [];
                $model['discount_rules'] = [];
                $model['product_categories'] = [];
                $model['product_tags'] = [];
                $model['product_variants'] = [];

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
                            'value' => $product_attribute['option_value'],
                            'image' => $product_attribute['option_image'],
                        ];

                        if (! $attribute_exists) {
                            $attribute_data = [
                                'id' => $product_attribute['id'],
                                'name' => $product_attribute['name'],
                                'slug' => $product_attribute['slug'],
                                'type' => strtolower(ProductAttributeType::fromValue((int) $product_attribute['type'])->key),
                                'image' => $product_attribute['image'],
                                'options' => [],
                            ];

                            if ($options_data['id']) {
                                array_push($attribute_data['options'], $options_data);
                            }

                            array_push($model['product_attributes'], $attribute_data);
                        } else {
                            if ($options_data['id']) {
                                foreach ($model['product_attributes'] as &$attribute) {
                                    if (! in_array($options_data['id'], array_column($attribute['options'], 'id'))
                                        && $attribute['id'] === $product_attribute['option_product_attribute_id']
                                    ) {
                                        array_push($attribute['options'], $options_data);

                                        break;
                                    }
                                }
                            }
                        }
                    }
                }

                foreach ($discount_rules as $discount_rule) {
                    if ($discount_rule['product_id'] === $modelId) {
                        unset($discount_rule['product_id']);
                        array_push($model['discount_rules'], $discount_rule);
                    }
                }

                foreach ($product_categories as $product_category) {
                    if ($product_category['product_id'] === $modelId) {
                        $category = [
                            'id' => $product_category['id'],
                            'name' => $product_category['name'],
                            'slug' => $product_category['slug'],
                            'parent_id' => $product_category['parent_id'],
                            'description' => $product_category['description'],
                            'menu_image' => $product_category['menu_image'],
                            'header_image' => $product_category['header_image'],
                            'discount_rules' => [],
                        ];

                        if (isset($product_category['discount_rule_id'])) {
                            array_push($category['discount_rules'], [
                                'id' => $product_category['discount_rule_id'],
                                'amount' => $product_category['discount_rule_amount'],
                                'type' => $product_category['discount_rule_type'],
                            ]);
                        }

                        array_push($model['product_categories'], $category);
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
                        if (! in_array($product_variant['id'], array_column($model['product_variants'], 'id'))) {
                            $variantData = [
                                'id' => $product_variant['id'],
                                'slug' => $product_variant['slug'],
                                'price' => $product_variant['price'],
                                'data' => $product_variant['data'],
                                'stock' => $product_variant['stock'],
                                'description' => $product_variant['description'],
                                'sku' => $product_variant['sku'],
                                'final_price' => $this->calculateFinalPrice($model, $product_variant['price']),
                                'product_attributes' => [],
                            ];

                            array_push($model['product_variants'], $variantData);
                        }

                        $attribute_option = [
                            'id' => $product_variant['product_attribute_option_id'],
                            'name' => $product_variant['product_attribute_option_name'],
                            'slug' => $product_variant['product_attribute_option_slug'],
                            'value' => $product_variant['product_attribute_option_value'],
                            'image' => $product_variant['product_attribute_option_image'],
                        ];

                        if ($product_variant['product_attribute_id']) {
                            foreach ($model['product_variants'] as &$model_variant) {
                                if ($model_variant['id'] === $product_variant['id']) {
                                    if (! in_array($product_variant['product_attribute_id'], array_column($model_variant['product_attributes'], 'id'))) {
                                        $attributeData = [
                                            'id' => $product_variant['product_attribute_id'],
                                            'name' => $product_variant['product_attribute_name'],
                                            'slug' => $product_variant['product_attribute_slug'],
                                            'type' => strtolower(ProductAttributeType::fromValue((int) $product_variant['product_attribute_type'])->key),
                                            'image' => $product_variant['product_attribute_image'],
                                            'options' => [],
                                        ];

                                        if ($attribute_option['id']) {
                                            array_push($attributeData['options'], $attribute_option);
                                        }

                                        array_push($model_variant['product_attributes'], $attributeData);
                                    } else {
                                        foreach ($model_variant['product_attributes'] as &$attribute) {
                                            if (! in_array($attribute_option['id'], array_column($attribute['options'], 'id'))
                                                && $attribute['id'] === $product_variant['product_attribute_id']
                                            ) {
                                                array_push($attribute['options'], $attribute_option);

                                                break;
                                            }
                                        }
                                    }

                                    break;
                                }
                            }
                        } elseif (is_null($product_variant['product_attribute_id']) && $attribute_option['id']) {
                            foreach ($model['product_variants'] as &$model_variant) {
                                foreach ($model_variant['product_attributes'] as &$attribute) {
                                    if (
                                        ! in_array($attribute_option['id'], array_column($attribute['options'], 'id'))
                                        && $attribute['id'] === $product_variant['product_attribute_id']
                                    ) {
                                        array_push($attribute['options'], $attribute_option);

                                        break;
                                    }
                                }
                            }
                        }
                    }
                }

                $model['final_price'] = $this->calculateFinalPrice($model);
            }

            return $result;
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * Calculate the final price
     */
    public function calculateFinalPrice(array $product, $price = false): string
    {
        if (! $price) {
            $price = $product['price'];
        }

        $discount_rules = [];

        if (! empty($product['discount_rules'])) {
            $discount_rules = collect($product['discount_rules'])->map(fn ($rule) => [
                'id' => $rule['id'],
                'amount' => $rule['amount'],
                'type' => $rule['type'],
            ])->toArray();
        }

        if (! empty($product['product_categories'])) {
            foreach ($product['product_categories'] as $category) {
                if (! empty($category['discount_rules'])) {
                    $discount_rules = array_merge($discount_rules, collect($category['discount_rules'])->map(fn ($rule) => [
                        'id' => $rule['id'],
                        'amount' => $rule['amount'],
                        'type' => $rule['type'],
                    ])->toArray());
                }
            }
        }

        $basePrice = $price;

        if (! empty($discount_rules)) {
            $discounts = array_map(function ($rule) use ($basePrice) {
                return $basePrice - GeneralHelper::getDiscountedValue($rule['type'], $rule['amount'], $basePrice);
            }, array_unique($discount_rules, SORT_REGULAR));

            $price = $price - $this->calculateDiscountAmount($discounts);
        }

        return number_format((float) $price, 2, '.', '');
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
            "{$this->model_table}.short_description",
            "{$this->model_table}.description",
            "{$this->model_table}.price",
            "{$this->model_table}.status",
            "{$this->model_table}.purchase_count",
            "{$this->model_table}.stock",
            "{$this->model_table}.backup_stock",
            "{$this->model_table}.sku",
            "{$this->model_table}.headline",
            "{$this->model_table}.subtitle",
            "{$this->model_table}.cover_photo",
        ];

        return $withTableNamePrefix
            ? $columns
            : array_map(function ($column_name) {
                return str_replace($this->model_table.'.', '', $column_name);
            }, $columns);
    }

    /**
     * Calculate the total discounts
     */
    private function calculateDiscountAmount($discounts): mixed
    {
        if (config('shoptopus.discount_rules.allow_discount_stacking') === true) {
            // Discounts stacked and all applied
            return array_sum($discounts);
        } else {
            // Only one discount applied. It is either the lowest or the highest
            return match (config('shoptopus.discount_rules.applied_discount')) {
                'highest' => max($discounts),
                default => min($discounts),
            };
        }
    }
}
