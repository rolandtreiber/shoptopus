<?php

namespace Tests\Feature\AdminBaseCRUD;

use App\Enums\DiscountTypes;
use App\Models\DiscountRule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\AdminControllerTestCase;

/**
 * @group admin-base-crud
 * @group discount_rules
 * @see \App\Http\Controllers\Admin\DiscountRuleController
 */
class DiscountRuleControllerTest extends AdminControllerTestCase
{
    /**
     * @test
     */
    public function test_discount_rules_can_be_listed()
    {
        $rules = DiscountRule::factory()->count(3)->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->get(route('admin.api.index.discount-rules', [
            'page' => 1,
            'paginate' => 20,
            'filters' => []
        ]));
        $response->assertJsonFragment([
            'id' => $rules[0]->id
        ]);
        $response->assertJsonFragment([
            'id' => $rules[1]->id
        ]);
        $response->assertJsonFragment([
            'id' => $rules[2]->id
        ]);
    }

    /**
     * @test
     */
    public function test_discount_rule_can_be_shown()
    {
        $rule = DiscountRule::factory()->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->get(route('admin.api.show.discount-rule', [
            'discountRule' => $rule->id,
        ]));
        $response->assertJsonFragment([
            'id' => $rule->id
        ]);
        $response
            ->assertJson(fn (AssertableJson $json) =>
            $json->where('data.id', $rule->id)
                ->where('data.type', $rule->type)
                ->etc());
        if ($rule->type === DiscountTypes::Amount) {
            $response
                ->assertJson(fn (AssertableJson $json) =>
                $json->where('data.amount', '£'.$rule->amount)
                    ->etc());
        } else {
            $response
                ->assertJson(fn (AssertableJson $json) =>
                $json->where('data.amount', $rule->amount.'%')
                    ->etc());
        }
    }

    /**
     * @test
     */
    public function test_discount_rule_can_be_created()
    {
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $validFrom = Carbon::now();
        $validUntil = Carbon::now()->addMonth();
        $response = $this->post(route('admin.api.create.discount-rule'), [
            'name' => json_encode([
                'en' => '5% off',
                'de' => '5% rabatt'
            ]),
            'type' => DiscountTypes::Percentage,
            'amount' => 5,
            'valid_from' => $validFrom,
            'valid_until' => $validUntil,
            'enabled' => true
        ]);
        $response->assertCreated();
        $ruleId = $response->json()['data']['id'];
        $rule = DiscountRule::find($ruleId);
        $this->assertEquals('5% off', $rule->setLocale('en')->name);
        $this->assertEquals('5% rabatt', $rule->setLocale('de')->name);
        $this->assertEquals(DiscountTypes::Percentage, $rule->type);
        $this->assertEquals(5, $rule->amount);
        $this->assertEquals($validFrom->format('Y-m-d H:i:s'), $rule->valid_from);
        $this->assertEquals($validUntil->format('Y-m-d H:i:s'), $rule->valid_until);
    }

    /**
     * @test
     */
    public function test_discount_rule_can_be_updated()
    {
        $rule = DiscountRule::factory()->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $validFrom = Carbon::now();
        $validUntil = Carbon::now()->addMonth();
        $response = $this->patch(route('admin.api.update.discount-rule', [
            'discountRule' => $rule
        ]), [
            'name' => json_encode([
                'en' => '5% off UPDATED',
                'de' => '5% rabatt AKTUALISIERT'
            ]),
            'type' => DiscountTypes::Percentage,
            'amount' => 8.5,
            'valid_from' => $validFrom,
            'valid_until' => $validUntil
        ]);
        $response->assertOk();
        $ruleId = $response->json()['data']['id'];
        $rule = DiscountRule::find($ruleId);
        $this->assertEquals('5% off UPDATED', $rule->setLocale('en')->name);
        $this->assertEquals('5% rabatt AKTUALISIERT', $rule->setLocale('de')->name);
        $this->assertEquals(DiscountTypes::Percentage, $rule->type);
        $this->assertEquals(8.5, $rule->amount);
        $this->assertEquals($validFrom->format('Y-m-d H:i:s'), $rule->valid_from);
        $this->assertEquals($validUntil->format('Y-m-d H:i:s'), $rule->valid_until);
    }

    /**
     * @test
     */
    public function test_discount_rule_can_be_deleted()
    {
        $rule = DiscountRule::factory()->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $this->delete(route('admin.api.delete.discount-rule', $rule));

        $this->assertSoftDeleted($rule);
    }

    /**
     * @test
     */
    public function test_discount_rule_creation_requires_appropriate_permissions()
    {
        $this->actingAs(User::where('email', 'storeassistant@m.com')->first());
        $validFrom = Carbon::now();
        $validUntil = Carbon::now()->addMonth();
        $response = $this->post(route('admin.api.create.discount-rule'), [
            'name' => json_encode([
                'en' => '5% off',
                'de' => '5% rabatt'
            ]),
            'type' => DiscountTypes::Percentage,
            'amount' => 5,
            'valid_from' => $validFrom,
            'valid_until' => $validUntil
        ]);
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_discount_rule_updating_requires_appropriate_permissions()
    {
        $rule = DiscountRule::factory()->create();
        $this->actingAs(User::where('email', 'storeassistant@m.com')->first());
        $validFrom = Carbon::now();
        $validUntil = Carbon::now()->addMonth();
        $response = $this->patch(route('admin.api.update.discount-rule', [
            'discountRule' => $rule
        ]), [
            'name' => json_encode([
                'en' => '5% off UPDATED',
                'de' => '5% rabatt AKTUALISIERT'
            ]),
            'type' => DiscountTypes::Percentage,
            'amount' => 8.5,
            'valid_from' => $validFrom,
            'valid_until' => $validUntil
        ]);
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_discount_rule_deletion_requires_appropriate_permissions()
    {
        $rule = DiscountRule::factory()->create();
        $this->actingAs(User::where('email', 'storeassistant@m.com')->first());
        $response = $this->delete(route('admin.api.delete.discount-rule', $rule));

        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_discount_rule_creation_validation()
    {
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $validFrom = Carbon::now();
        $validUntil = Carbon::now()->addMonth();
        $response = $this->post(route('admin.api.create.discount-rule'), [
            'name' => json_encode([
                'en' => '5% off',
                'de' => '5% rabatt'
            ]),
            'type' => DiscountTypes::Percentage,
            'valid_from' => $validFrom,
            'valid_until' => $validUntil
        ]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_discount_rule_update_validation()
    {
        $rule = DiscountRule::factory()->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $validFrom = Carbon::now();
        $validUntil = Carbon::now()->addMonth();
        $response = $this->patch(route('admin.api.update.discount-rule', [
            'discountRule' => $rule
        ]), [
            'name' => json_encode([
                'en' => '5% off UPDATED',
                'de' => '5% rabatt AKTUALISIERT'
            ]),
            'type' => DiscountTypes::Percentage,
            'amount' => "Thirty two",
            'valid_from' => $validFrom,
            'valid_until' => $validUntil
        ]);
        $response->assertStatus(422);
    }

}
