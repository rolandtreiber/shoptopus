<?php

namespace App\Repositories\Local\Product;

use App\Enums\DiscountType;
use App\Enums\ProductAttributeType;
use App\Models\DiscountRule;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeOption;
use App\Models\ProductTag;
use Carbon\Carbon;

class OLDProductRepository
{
    /**
     * Merge the product attributes into the corresponding products
     */
    private function addAttributesToProduct(array $data, array &$products): void
    {
        $attribute_option = [
            'id' => $data['product_attribute_option_id'],
            'name' => $data['product_attribute_option_name'],
            'slug' => $data['product_attribute_option_slug'],
            'value' => $data['product_attribute_option_value'],
            'image' => $data['product_attribute_option_image'],
        ];

        if ($data['product_product_attribute_id'] && $data['product_attribute_id']) {
            foreach ($products as &$model_product) {
                if ($model_product['id'] === $data['id']) {
                    if (! in_array($data['product_attribute_id'], array_column($model_product['product_attributes'], 'id'))) {
                        $relationData = [
                            'id' => $data['product_attribute_id'],
                            'name' => $data['product_attribute_name'],
                            'slug' => $data['product_attribute_slug'],
                            'type' => strtolower(ProductAttributeType::fromValue((int) $data['product_attribute_type'])->key),
                            'image' => $data['product_attribute_image'],
                            'options' => [],
                        ];

                        if ($attribute_option['id']) {
                            array_push($relationData['options'], $attribute_option);
                        }

                        array_push($model_product['product_attributes'], $relationData);
                    } else {
                        foreach ($model_product['product_attributes'] as &$attribute) {
                            if ($attribute['id'] === $data['product_attribute_id']) {
                                array_push($attribute['options'], $attribute_option);

                                break;
                            }
                        }
                    }

                    break;
                }
            }
        } elseif ($data['product_product_attribute_id'] && is_null($data['product_attribute_id']) && $attribute_option['id']) {
            foreach ($products as &$model_product) {
                foreach ($model_product['product_attributes'] as &$attribute) {
                    if ($attribute['id'] === $data['product_product_attribute_id']) {
                        array_push($attribute['options'], $attribute_option);

                        break;
                    }
                }
            }
        }
    }

    /**
     * Merge the discount rules into the corresponding products
     */
    private function addDiscountRuleToProduct(array $data, array &$products): void
    {
        if ($data['discount_rule_id']) {
            $is_valid = Carbon::now()->isAfter(Carbon::createFromFormat('Y-m-d H:i:s', $data['discount_rule_valid_from']))
                && Carbon::now()->isBefore(Carbon::createFromFormat('Y-m-d H:i:s', $data['discount_rule_valid_until']));

            if (! $is_valid) {
                return;
            }

            foreach ($products as &$model_product) {
                if ($model_product['id'] === $data['id']) {
                    if (! in_array($data['discount_rule_id'], array_column($model_product['discount_rules'], 'id'))) {
                        $relationData = [
                            'id' => $data['discount_rule_id'],
                            'type' => strtolower(DiscountType::fromValue((int) $data['discount_rule_type'])->key),
                            'name' => $data['discount_rule_name'],
                            'slug' => $data['discount_rule_slug'],
                            'amount' => $data['discount_rule_amount'],
                            'valid_from' => $data['discount_rule_valid_from'],
                            'valid_until' => $data['discount_rule_valid_until'],
                        ];

                        array_push($model_product['discount_rules'], $relationData);
                    }

                    break;
                }
            }
        }
    }

    /**
     * Merge the product tags into the corresponding products
     */
    private function addTagsToProduct(array $data, array &$products): void
    {
        if ($data['product_tag_id']) {
            foreach ($products as &$model_product) {
                if ($model_product['id'] === $data['id']) {
                    if (! in_array($data['product_tag_id'], array_column($model_product['product_tags'], 'id'))) {
                        $relationData = [
                            'id' => $data['product_tag_id'],
                            'name' => $data['product_tag_name'],
                            'slug' => $data['product_tag_slug'],
                            'description' => $data['product_tag_description'],
                            'badge' => $data['product_tag_badge'],
                            'display_badge' => $data['product_tag_display_badge'],
                        ];

                        array_push($model_product['product_tags'], $relationData);
                    }

                    break;
                }
            }
        }
    }

//    TESTS

    /**
     * @test
     *
     * @group apiGet
     */
    public function it_returns_all_the_valid_discount_rules_for_the_associated_products()
    {
        $p = Product::factory()->create();

        $dr_valid = DiscountRule::factory()->create(['valid_from' => now()->subDay(), 'valid_until' => now()->addMonth()]);
        $dr_not_yet_valid = DiscountRule::factory()->create(['valid_from' => now()->addDay(), 'valid_until' => now()->addMonth()]);
        $dr_disabled = DiscountRule::factory()->create(['valid_from' => now()->subDay(), 'valid_until' => now()->addMonth(), 'enabled' => false]);
        $dr_deleted = DiscountRule::factory()->create(['valid_from' => now()->subDay(), 'valid_until' => now()->addMonth(), 'deleted_at' => now()]);

        $p->discount_rules()->attach([$dr_valid->id, $dr_not_yet_valid->id, $dr_disabled->id, $dr_deleted->id]);

        $this->product_category->products()->attach($p->id);

        $res = $this->sendRequest();

        $res->assertJsonStructure([
            'data' => [
                [
                    'products' => [
                        [
                            'discount_rules' => [
                                [
                                    'id',
                                    'type',
                                    'name',
                                    'amount',
                                    'valid_from',
                                    'valid_until',
                                    'slug',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertCount(1, $res->json('data.0.products.0.discount_rules'));
        $this->assertEquals($dr_valid->id, $res->json('data.0.products.0.discount_rules.0.id'));
    }

    /**
     * @test
     *
     * @group apiGet
     */
    public function it_returns_all_the_tags_for_the_associated_products()
    {
        $p = Product::factory()->create();
        $pt_valid = ProductTag::factory()->create();
        $pt_disabled = ProductTag::factory()->create(['enabled' => false]);
        $pt_deleted = ProductTag::factory()->create(['deleted_at' => now()]);

        $p->product_tags()->attach([$pt_valid->id, $pt_disabled->id, $pt_deleted->id]);

        $this->product_category->products()->attach($p->id);

        $res = $this->sendRequest();

        $res->assertJsonStructure([
            'data' => [
                [
                    'products' => [
                        [
                            'product_tags' => [
                                [
                                    'id',
                                    'slug',
                                    'name',
                                    'description',
                                    'badge',
                                    'display_badge',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertCount(1, $res->json('data.0.products.0.product_tags'));
        $this->assertEquals($pt_valid->id, $res->json('data.0.products.0.product_tags.0.id'));
    }

    /**
     * @test
     *
     * @group apiGet
     */
    public function it_returns_all_the_attributes_with_their_corresponding_options_for_the_associated_products()
    {
        $p = Product::factory()->create();

        $pa_valid = ProductAttribute::factory()->create();
        $valid_options = ProductAttributeOption::factory()->count(3)->create(['product_attribute_id' => $pa_valid->id]);

        $pa_disabled = ProductAttribute::factory()->create(['enabled' => false]);
        ProductAttributeOption::factory()->create(['product_attribute_id' => $pa_disabled->id]);
        $pa_deleted = ProductAttribute::factory()->create(['deleted_at' => now()]);
        ProductAttributeOption::factory()->create(['product_attribute_id' => $pa_deleted->id]);

        $p->product_attributes()->attach([$pa_valid->id, $pa_disabled->id, $pa_deleted->id]);

        $this->product_category->products()->attach($p->id);

        $res = $this->sendRequest();

        $res->assertJsonStructure([
            'data' => [
                [
                    'products' => [
                        [
                            'product_attributes' => [
                                [
                                    'id',
                                    'name',
                                    'slug',
                                    'type',
                                    'image',
                                    'options' => [
                                        [
                                            'id',
                                            'name',
                                            'slug',
                                            'value',
                                            'image',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertCount(1, $res->json('data.0.products.0.product_attributes'));
        $this->assertEquals($pa_valid->id, $res->json('data.0.products.0.product_attributes.0.id'));

        $this->assertCount(3, $res->json('data.0.products.0.product_attributes.0.options'));

        $valid_options->first()->update(['enabled' => false]);

        $this->assertCount(2, $this->sendRequest()->json('data.0.products.0.product_attributes.0.options'));
    }
}
