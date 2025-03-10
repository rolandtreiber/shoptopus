<?php

namespace Tests\Feature\AdminBaseCRUD;

use App\Enums\DiscountType;
use App\Models\DiscountRule;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\AdminControllerTestCase;

/**
 * @group admin-base-crud
 * @group discount_rules
 *
 * @see \App\Http\Controllers\Admin\DiscountRuleController
 */
class DiscountRuleControllerTest extends AdminControllerTestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function test_discount_rules_can_be_listed(): void
    {
        $rules = DiscountRule::factory()->count(3)->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->get(route('admin.api.index.discount-rules', [
            'page' => 1,
            'paginate' => 20,
            'filters' => [],
        ]));
        $response->assertJsonFragment([
            'id' => $rules[0]->id,
        ]);
        $response->assertJsonFragment([
            'id' => $rules[1]->id,
        ]);
        $response->assertJsonFragment([
            'id' => $rules[2]->id,
        ]);
    }

    /**
     * @test
     */
    public function test_discount_rule_can_be_shown(): void
    {
        $rule = DiscountRule::factory()->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->get(route('admin.api.show.discount-rule', [
            'discountRule' => $rule->id,
        ]));
        $response->assertJsonFragment([
            'id' => $rule->id,
        ]);
        $response
            ->assertJson(fn (AssertableJson $json) => $json->where('data.id', $rule->id)
                ->where('data.type', $rule->type)
                ->etc());
        if ($rule->type === DiscountType::Amount) {
            $response
                ->assertJson(fn (AssertableJson $json) => $json->where('data.amount', '£'.number_format((float) $rule->amount, 2, '.', ''))
                    ->etc());
        } else {
            $response
                ->assertJson(fn (AssertableJson $json) => $json->where('data.amount', $rule->amount.'%')
                    ->etc());
        }
    }

    /**
     * @test
     */
    public function test_discount_rule_can_be_created(): void
    {
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $validFrom = Carbon::now();
        $validUntil = Carbon::now()->addMonth();
        $response = $this->post(route('admin.api.create.discount-rule'), [
            'name' => json_encode([
                'en' => '5% off',
                'de' => '5% rabatt',
            ]),
            'type' => DiscountType::Percentage,
            'amount' => 5,
            'valid_from' => $validFrom,
            'valid_until' => $validUntil,
            'enabled' => true,
        ]);
        $response->assertCreated();
        $ruleId = $response->json()['data']['id'];
        $rule = DiscountRule::find($ruleId);
        $this->assertEquals('5% off', $rule->setLocale('en')->name);
        $this->assertEquals('5% rabatt', $rule->setLocale('de')->name);
        $this->assertEquals(DiscountType::Percentage, $rule->type);
        $this->assertEquals(5, $rule->amount);
        $this->assertEquals($validFrom->format('Y-m-d H:i:s'), $rule->valid_from);
        $this->assertEquals($validUntil->format('Y-m-d H:i:s'), $rule->valid_until);
    }

    /**
     * @test
     */
    public function test_discount_rule_can_be_updated(): void
    {
        $rule = DiscountRule::factory()->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $validFrom = Carbon::now();
        $validUntil = Carbon::now()->addMonth();
        $response = $this->patch(route('admin.api.update.discount-rule', [
            'discountRule' => $rule,
        ]), [
            'name' => json_encode([
                'en' => '5% off UPDATED',
                'de' => '5% rabatt AKTUALISIERT',
            ]),
            'type' => DiscountType::Percentage,
            'amount' => 8.5,
            'valid_from' => $validFrom,
            'valid_until' => $validUntil,
        ]);
        $response->assertOk();
        $ruleId = $response->json()['data']['id'];
        $rule = DiscountRule::find($ruleId);
        $this->assertEquals('5% off UPDATED', $rule->setLocale('en')->name);
        $this->assertEquals('5% rabatt AKTUALISIERT', $rule->setLocale('de')->name);
        $this->assertEquals(DiscountType::Percentage, $rule->type);
        $this->assertEquals(8.5, $rule->amount);
        $this->assertEquals($validFrom->format('Y-m-d H:i:s'), $rule->valid_from);
        $this->assertEquals($validUntil->format('Y-m-d H:i:s'), $rule->valid_until);
    }

    /**
     * @test
     */
    public function test_discount_rule_can_be_deleted(): void
    {
        $rule = DiscountRule::factory()->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $this->delete(route('admin.api.delete.discount-rule', $rule));

        $this->assertSoftDeleted($rule);
    }

    /**
     * @test
     */
    public function test_discount_rule_creation_requires_appropriate_Permission(): void
    {
        $this->actingAs(User::where('email', 'storeassistant@m.com')->first());
        $validFrom = Carbon::now();
        $validUntil = Carbon::now()->addMonth();
        $response = $this->post(route('admin.api.create.discount-rule'), [
            'name' => json_encode([
                'en' => '5% off',
                'de' => '5% rabatt',
            ]),
            'type' => DiscountType::Percentage,
            'amount' => 5,
            'valid_from' => $validFrom,
            'valid_until' => $validUntil,
        ]);
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_discount_rule_updating_requires_appropriate_Permission(): void
    {
        $rule = DiscountRule::factory()->create();
        $this->actingAs(User::where('email', 'storeassistant@m.com')->first());
        $validFrom = Carbon::now();
        $validUntil = Carbon::now()->addMonth();
        $response = $this->patch(route('admin.api.update.discount-rule', [
            'discountRule' => $rule,
        ]), [
            'name' => json_encode([
                'en' => '5% off UPDATED',
                'de' => '5% rabatt AKTUALISIERT',
            ]),
            'type' => DiscountType::Percentage,
            'amount' => 8.5,
            'valid_from' => $validFrom,
            'valid_until' => $validUntil,
        ]);
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_discount_rule_deletion_requires_appropriate_Permission(): void
    {
        $rule = DiscountRule::factory()->create();
        $this->actingAs(User::where('email', 'storeassistant@m.com')->first());
        $response = $this->delete(route('admin.api.delete.discount-rule', $rule));

        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_discount_rule_creation_validation(): void
    {
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $validFrom = Carbon::now();
        $validUntil = Carbon::now()->addMonth();
        $response = $this->post(route('admin.api.create.discount-rule'), [
            'name' => json_encode([
                'en' => '5% off',
                'de' => '5% rabatt',
            ]),
            'type' => DiscountType::Percentage,
            'valid_from' => $validFrom,
            'valid_until' => $validUntil,
        ]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_discount_rule_update_validation(): void
    {
        $rule = DiscountRule::factory()->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $validFrom = Carbon::now();
        $validUntil = Carbon::now()->addMonth();
        $response = $this->patch(route('admin.api.update.discount-rule', [
            'discountRule' => $rule,
        ]), [
            'name' => json_encode([
                'en' => '5% off UPDATED',
                'de' => '5% rabatt AKTUALISIERT',
            ]),
            'type' => DiscountType::Percentage,
            'amount' => 'Thirty two',
            'valid_from' => $validFrom,
            'valid_until' => $validUntil,
        ]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_available_categories_can_be_listed_for_discount_rule(): void
    {
        /** @var DiscountRule $rule */
        $rule = DiscountRule::factory()->create();
        $productCategories = ProductCategory::factory()->count(3)->create();
        $rule->categories()->attach($productCategories[0]);
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->get(route('admin.api.show.discount-rule.available-categories', [
            'discountRule' => $rule
        ]));
        $response
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data.0.id', $productCategories[1]->id)
                ->where('data.1.id', $productCategories[2]->id)
                ->etc());
    }

    /**
     * @test
     */
    public function test_available_products_can_be_listed_for_discount_rule(): void
    {
        /** @var DiscountRule $rule */
        $rule = DiscountRule::factory()->create();
        $products = Product::factory()->count(3)->create();
        $rule->products()->attach($products[0]);
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->get(route('admin.api.show.discount-rule.available-products', [
            'discountRule' => $rule
        ]));
        $response
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data.0.id', $products[1]->id)
                ->where('data.1.id', $products[2]->id)
                ->etc());
    }

    /**
     * @test
     */
    public function test_category_can_be_attached_to_discount_rule(): void
    {
        /** @var DiscountRule $rule */
        $rule = DiscountRule::factory()->create();
        /** @var ProductCategory $productCategory */
        $productCategory = ProductCategory::factory()->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->post(route('admin.api.discount-rule.attach-product-category', [
            'discountRule' => $rule,
            'productCategory' => $productCategory
        ]));
        $this->assertDatabaseHas('discount_rule_product_category', [
            'discount_rule_id' => $rule->id,
           'product_category_id' => $productCategory->id
        ]);
        $response
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data.categories.0.id', $productCategory->id)
                ->etc());
    }

    /**
     * @test
     */
    public function test_category_can_be_detached_from_discount_rule(): void
    {
        /** @var DiscountRule $rule */
        $rule = DiscountRule::factory()->create();
        /** @var ProductCategory $productCategory */
        $productCategory = ProductCategory::factory()->create();
        $rule->categories()->attach($productCategory);
        $this->assertDatabaseHas('discount_rule_product_category', [
            'discount_rule_id' => $rule->id,
            'product_category_id' => $productCategory->id
        ]);
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->delete(route('admin.api.discount-rule.detach-product-category', [
            'discountRule' => $rule,
            'productCategory' => $productCategory
        ]));
        $response->assertOk();
        $response
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data.id', $rule->id)
                ->count('data.categories', 0)
                ->etc());
        $this->assertDatabaseMissing('discount_rule_product_category', [
            'discount_rule_id' => $rule->id,
            'product_category_id' => $productCategory->id
        ]);
    }

    /**
     * @test
     */
    public function test_product_can_be_attached_to_discount_rule(): void
    {
        /** @var DiscountRule $rule */
        $rule = DiscountRule::factory()->create();
        /** @var Product $product */
        $product = Product::factory()->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->post(route('admin.api.discount-rule.attach-product', [
            'discountRule' => $rule,
            'product' => $product
        ]));
        $this->assertDatabaseHas('discount_rule_product', [
            'discount_rule_id' => $rule->id,
            'product_id' => $product->id
        ]);
        $response
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data.products.0.id', $product->id)
                ->etc());
    }

    /**
     * @test
     */
    public function test_product_can_be_detached_from_discount_rule(): void
    {
        /** @var DiscountRule $rule */
        $rule = DiscountRule::factory()->create();
        /** @var ProductCategory $product */
        $product = Product::factory()->create();
        $rule->products()->attach($product);
        $this->assertDatabaseHas('discount_rule_product', [
            'discount_rule_id' => $rule->id,
            'product_id' => $product->id
        ]);
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->delete(route('admin.api.discount-rule.detach-product', [
            'discountRule' => $rule,
            'product' => $product
        ]));
        $response->assertOk();
        $response
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data.id', $rule->id)
                ->count('data.products', 0)
                ->etc());
        $this->assertDatabaseMissing('discount_rule_product', [
            'discount_rule_id' => $rule->id,
            'product_id' => $product->id
        ]);
    }

    /**
     * @test
     */
    public function test_category_attaching_and_detaching_requires_authentication(): void
    {
        /** @var DiscountRule $rule */
        $rule = DiscountRule::factory()->create();
        /** @var ProductCategory $productCategory */
        $productCategory = ProductCategory::factory()->create();
        $response = $this->post(route('admin.api.discount-rule.attach-product-category', [
            'discountRule' => $rule,
            'productCategory' => $productCategory
        ]));
        $response->assertStatus(500);

        $rule->categories()->attach($productCategory);
        $this->assertDatabaseHas('discount_rule_product_category', [
            'discount_rule_id' => $rule->id,
            'product_category_id' => $productCategory->id
        ]);
        $response = $this->delete(route('admin.api.discount-rule.detach-product-category', [
            'discountRule' => $rule,
            'productCategory' => $productCategory
        ]));
        $response->assertStatus(500);
    }
    /**
     * @test
     */
    public function test_category_attaching_and_detaching_requires_super_user_role(): void
    {
        /** @var DiscountRule $rule */
        $rule = DiscountRule::factory()->create();
        /** @var ProductCategory $productCategory */
        $productCategory = ProductCategory::factory()->create();
        $this->actingAs(User::where('email', 'customer@m.com')->first());
        $response = $this->post(route('admin.api.discount-rule.attach-product-category', [
            'discountRule' => $rule,
            'productCategory' => $productCategory
        ]));
        $response->assertForbidden();

        $rule->categories()->attach($productCategory);
        $this->assertDatabaseHas('discount_rule_product_category', [
            'discount_rule_id' => $rule->id,
            'product_category_id' => $productCategory->id
        ]);
        $response = $this->delete(route('admin.api.discount-rule.detach-product-category', [
            'discountRule' => $rule,
            'productCategory' => $productCategory
        ]));
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_product_attaching_and_detaching_requires_authentication(): void
    {
        /** @var DiscountRule $rule */
        $rule = DiscountRule::factory()->create();
        /** @var Product $product */
        $product = Product::factory()->create();
        $response = $this->post(route('admin.api.discount-rule.attach-product', [
            'discountRule' => $rule,
            'product' => $product
        ]));
        $response->assertStatus(500);

        $rule->products()->attach($product);
        $response = $this->delete(route('admin.api.discount-rule.detach-product', [
            'discountRule' => $rule,
            'product' => $product
        ]));

        $response->assertStatus(500);
    }

    /**
     * @test
     */
    public function test_product_attaching_and_detaching_requires_super_user_role(): void
    {
        /** @var DiscountRule $rule */
        $rule = DiscountRule::factory()->create();
        /** @var Product $product */
        $product = Product::factory()->create();
        $this->actingAs(User::where('email', 'customer@m.com')->first());
        $response = $this->post(route('admin.api.discount-rule.attach-product', [
            'discountRule' => $rule,
            'product' => $product
        ]));
        $response->assertForbidden();

        $rule->products()->attach($product);
        $response = $this->delete(route('admin.api.discount-rule.detach-product', [
            'discountRule' => $rule,
            'product' => $product
        ]));

        $response->assertForbidden();
    }

}
