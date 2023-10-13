<?php

namespace Tests\Feature\AdminBaseCRUD;

use App\Models\DeliveryRule;
use App\Models\DeliveryType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\AdminControllerTestCase;

/**
 * @group admin-base-crud
 * @group delivery_rules
 *
 * @see \App\Http\Controllers\Admin\DeliveryRuleController
 */
class DeliveryRuleControllerTest extends AdminControllerTestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function test_delivery_rules_can_be_listed(): void
    {
        $deliveryType = DeliveryType::factory()->create();
        $deliveryRules = DeliveryRule::factory()->state([
            'delivery_type_id' => $deliveryType->id,
        ])->count(3)->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->get(route('admin.api.index.delivery-rules', [
            'deliveryType' => $deliveryType,
            'page' => 1,
            'paginate' => 20,
            'filters' => [],
        ]));
        $response->assertJsonFragment([
            'id' => $deliveryRules[0]->id,
        ]);
        $response->assertJsonFragment([
            'id' => $deliveryRules[1]->id,
        ]);
        $response->assertJsonFragment([
            'id' => $deliveryRules[2]->id,
        ]);
    }

    /**
     * @test
     */
    public function test_delivery_rule_can_be_shown(): void
    {
        $deliveryType = DeliveryType::factory()->create();
        $deliveryRule = DeliveryRule::factory([
            'delivery_type_id' => $deliveryType->id,
        ])->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->get(route('admin.api.show.delivery-rule', [
            'deliveryType' => $deliveryType->id,
            'deliveryRule' => $deliveryRule->id,
        ]));
        $response->assertJsonFragment([
            'id' => $deliveryRule->id,
        ]);
        $response
            ->assertJson(fn (AssertableJson $json) => $json->where('data.id', $deliveryRule->id)
                ->where('data.min_weight', $deliveryRule->min_weight)
                ->where('data.max_weight', $deliveryRule->max_weight)
                ->etc());
    }

    /**
     * @test
     */
    public function test_delivery_rule_can_be_created(): void
    {
        $deliveryType = DeliveryType::factory()->create();
        $postCodes = ['BS10', 'NX9'];
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->post(route('admin.api.create.delivery-rule', [
            'deliveryType' => $deliveryType,
        ]), [
            'postcodes' => $postCodes,
            'enabled' => true,
            'min_weight' => 0,
            'max_weight' => 5000,
            'min_distance' => 10,
            'max_distance' => 100,
            'lat' => 51.4545,
            'lon' => -2.5879,
        ]);
        $response->assertCreated();
        $deliveryRuleId = $response->json()['data']['id'];
        $rule = DeliveryRule::find($deliveryRuleId);
        $this->assertTrue($rule->enabled);
        $this->assertEquals($postCodes, $rule->postcodes);
        $this->assertEquals(0, $rule->min_weight);
        $this->assertEquals(5000, $rule->max_weight);
        $this->assertEquals(10, $rule->min_distance);
        $this->assertEquals(100, $rule->max_distance);
        $this->assertEquals(51.4545, $rule->lat);
        $this->assertEquals(-2.5879, $rule->lon);
    }

    /**
     * @test
     */
    public function test_delivery_rule_can_be_updated(): void
    {
        $deliveryType = DeliveryType::factory()->create();
        $deliveryRule = DeliveryRule::factory([
            'delivery_type_id' => $deliveryType->id,
        ])->create();
        $postCodes = ['BS10', 'NX9'];
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->patch(route('admin.api.update.delivery-rule', [
            'deliveryType' => $deliveryType,
            'deliveryRule' => $deliveryRule,
        ]), [
            'postcodes' => $postCodes,
            'enabled' => true,
            'min_weight' => 0,
            'max_weight' => 5000,
            'min_distance' => 10,
            'max_distance' => 100,
            'lat' => 51.4545,
            'lon' => -2.5879,
        ]);
        $response->assertOk();
        $deliveryRuleId = $response->json()['data']['id'];
        $rule = DeliveryRule::find($deliveryRuleId);
        $this->assertTrue($rule->enabled);
        $this->assertEquals($postCodes, $rule->postcodes);
        $this->assertEquals(0, $rule->min_weight);
        $this->assertEquals(5000, $rule->max_weight);
        $this->assertEquals(10, $rule->min_distance);
        $this->assertEquals(100, $rule->max_distance);
        $this->assertEquals(51.4545, $rule->lat);
        $this->assertEquals(-2.5879, $rule->lon);
    }

    /**
     * @test
     */
    public function test_delivery_rule_can_be_deleted(): void
    {
        $deliveryType = DeliveryType::factory()->create();
        $deliveryRule = DeliveryRule::factory([
            'delivery_type_id' => $deliveryType->id,
        ])->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $this->delete(route('admin.api.delete.delivery-rule', [
            $deliveryType,
            $deliveryRule,
        ]));

        $this->assertSoftDeleted($deliveryRule);
    }

    /**
     * @test
     */
    public function test_delivery_rule_creation_requires_appropriate_Permission(): void
    {
        $this->actingAs(User::where('email', 'customer@m.com')->first());
        $deliveryType = DeliveryType::factory()->create();
        $postCodes = ['BS10', 'NX9'];
        $response = $this->post(route('admin.api.create.delivery-rule', [
            'deliveryType' => $deliveryType,
        ]), [
            'postcodes' => $postCodes,
            'enabled' => true,
            'min_weight' => 0,
            'max_weight' => 5000,
            'min_distance' => 10,
            'max_distance' => 100,
            'lat' => 51.4545,
            'lon' => -2.5879,
        ]);
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_delivery_rule_updating_requires_appropriate_Permission(): void
    {
        $deliveryType = DeliveryType::factory()->create();
        $deliveryRule = DeliveryRule::factory([
            'delivery_type_id' => $deliveryType->id,
        ])->create();
        $postCodes = ['BS10', 'NX9'];
        $this->actingAs(User::where('email', 'customer@m.com')->first());
        $response = $this->patch(route('admin.api.update.delivery-rule', [
            'deliveryType' => $deliveryType,
            'deliveryRule' => $deliveryRule,
        ]), [
            'postcodes' => $postCodes,
            'enabled' => true,
            'min_weight' => 0,
            'max_weight' => 5000,
            'min_distance' => 10,
            'max_distance' => 100,
            'lat' => 51.4545,
            'lon' => -2.5879,
        ]);
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_delivery_rule_deletion_requires_appropriate_Permission(): void
    {
        $deliveryType = DeliveryType::factory()->create();
        $deliveryRule = DeliveryRule::factory([
            'delivery_type_id' => $deliveryType->id,
        ])->create();
        $this->actingAs(User::where('email', 'customer@m.com')->first());
        $response = $this->delete(route('admin.api.delete.delivery-rule', [
            $deliveryType,
            $deliveryRule,
        ]));

        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_delivery_rule_creation_validation(): void
    {
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $deliveryType = DeliveryType::factory()->create();
        $response = $this->post(route('admin.api.create.delivery-rule', [
            'deliveryType' => $deliveryType,
        ]), [
            'lat' => 'hello',
            'lon' => -2.5879,
        ]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_delivery_rule_update_validation(): void
    {
        $deliveryType = DeliveryType::factory()->create();
        $deliveryRule = DeliveryRule::factory([
            'delivery_type_id' => $deliveryType->id,
        ])->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->patch(route('admin.api.update.delivery-rule', [
            'deliveryType' => $deliveryType,
            'deliveryRule' => $deliveryRule,
        ]), [
            'lat' => 51.4545,
            'lon' => 'invalid',
        ]);
        $response->assertStatus(422);
    }
}
