<?php

namespace Tests\Feature\AdminBaseCRUD;

use App\Enums\DeliveryTypeStatuses;
use App\Models\DeliveryType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\AdminControllerTestCase;

/**
 * @group admin-base-crud
 * @group delivery_types
 * @see \App\Http\Controllers\Admin\DeliveryTypeController
 */
class DeliveryTypeControllerTest extends AdminControllerTestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function test_delivery_types_can_be_listed()
    {
        $deliveryTypes = DeliveryType::factory()->count(3)->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->get(route('admin.api.index.delivery-types', [
            'page' => 1,
            'paginate' => 20,
            'filters' => []
        ]));
        $response->assertJsonFragment([
            'id' => $deliveryTypes[0]->id
        ]);
        $response->assertJsonFragment([
            'id' => $deliveryTypes[1]->id
        ]);
        $response->assertJsonFragment([
            'id' => $deliveryTypes[2]->id
        ]);
    }

    /**
     * @test
     */
    public function test_delivery_type_can_be_shown()
    {
        $deliveryType = DeliveryType::factory()->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->get(route('admin.api.show.delivery-type', [
            'deliveryType' => $deliveryType->id,
        ]));
        $response->assertJsonFragment([
            'id' => $deliveryType->id
        ]);
        $response
            ->assertJson(fn (AssertableJson $json) =>
            $json->where('data.id', $deliveryType->id)
                ->where('data.price', $deliveryType->price)
                ->where('data.enabled', $deliveryType->enabled)
                ->where('data.enabled_by_default_on_creation', $deliveryType->enabled_by_default_on_creation)
                ->etc());
    }

    /**
     * @test
     */
    public function test_delivery_type_can_be_created()
    {
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->post(route('admin.api.create.delivery-type'), [
            'name' => json_encode([
                'en' => 'Express overnight',
                'de' => 'Express Ubernacht'
            ]),
            'description' => json_encode([
                'en' => 'Very quick',
                'de' => 'Sehr schnell'
            ]),
            'price' => 15,
            'status' => DeliveryTypeStatuses::Enabled
        ]);
        $response->assertCreated();
        $deliveryTypeId = $response->json()['data']['id'];
        $rule = DeliveryType::find($deliveryTypeId);
        $this->assertEquals('Express overnight', $rule->setLocale('en')->name);
        $this->assertEquals('Express Ubernacht', $rule->setLocale('de')->name);
        $this->assertEquals('Very quick', $rule->setLocale('en')->description);
        $this->assertEquals('Sehr schnell', $rule->setLocale('de')->description);
        $this->assertEquals(true, $rule->enabled);
        $this->assertEquals(15, $rule->price);
    }

    /**
     * @test
     */
    public function test_delivery_type_can_be_updated()
    {
        $deliveryType = DeliveryType::factory()->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->patch(route('admin.api.update.delivery-type', [
            'deliveryType' => $deliveryType
        ]), [
            'name' => json_encode([
                'en' => 'Express overnight UPDATED',
                'de' => 'Express Ubernacht AKTUALISIERT'
            ]),
            'description' => json_encode([
                'en' => 'Very quick UPDATED',
                'de' => 'Sehr schnell AKTUALISIERT'
            ]),
            'enabled' => true,
            'price' => 15,
            'status' => DeliveryTypeStatuses::Enabled
        ]);
        $response->assertOk();
        $deliveryTypeId = $response->json()['data']['id'];
        $rule = DeliveryType::find($deliveryTypeId);
        $this->assertEquals('Express overnight UPDATED', $rule->setLocale('en')->name);
        $this->assertEquals('Express Ubernacht AKTUALISIERT', $rule->setLocale('de')->name);
        $this->assertEquals('Very quick UPDATED', $rule->setLocale('en')->description);
        $this->assertEquals('Sehr schnell AKTUALISIERT', $rule->setLocale('de')->description);
        $this->assertEquals(true, $rule->enabled);
        $this->assertEquals(15, $rule->price);
    }

    /**
     * @test
     */
    public function test_delivery_type_can_be_deleted()
    {
        $deliveryType = DeliveryType::factory()->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $this->delete(route('admin.api.delete.delivery-type', $deliveryType));

        $this->assertSoftDeleted($deliveryType);
    }

    /**
     * @test
     */
    public function test_delivery_type_creation_requires_appropriate_permissions()
    {
        $this->actingAs(User::where('email', 'storeassistant@m.com')->first());
        $response = $this->post(route('admin.api.create.delivery-type'), [
            'name' => json_encode([
                'en' => 'Express overnight',
                'de' => 'Express Ubernacht'
            ]),
            'description' => json_encode([
                'en' => 'Very quick',
                'de' => 'Sehr schnell'
            ]),
            'price' => 15,
            'status' => DeliveryTypeStatuses::Enabled
        ]);
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_delivery_type_updating_requires_appropriate_permissions()
    {
        $deliveryType = DeliveryType::factory()->create();
        $this->actingAs(User::where('email', 'storeassistant@m.com')->first());
        $response = $this->patch(route('admin.api.update.delivery-type', [
            'deliveryType' => $deliveryType
        ]), [
            'name' => json_encode([
                'en' => 'Express overnight UPDATED',
                'de' => 'Express Ubernacht AKTUALISIERT'
            ]),
            'description' => json_encode([
                'en' => 'Very quick UPDATED',
                'de' => 'Sehr schnell AKTUALISIERT'
            ]),
            'price' => 15,
            'status' => DeliveryTypeStatuses::Enabled
        ]);
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_delivery_type_deletion_requires_appropriate_permissions()
    {
        $deliveryType = DeliveryType::factory()->create();
        $this->actingAs(User::where('email', 'storeassistant@m.com')->first());
        $response = $this->delete(route('admin.api.delete.delivery-type', $deliveryType));

        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_delivery_type_creation_validation()
    {
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->post(route('admin.api.create.delivery-type'), [
            'description' => json_encode([
                'en' => 'Very quick',
                'de' => 'Sehr schnell'
            ]),
            'price' => 15,
            'status' => DeliveryTypeStatuses::Enabled
        ]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_delivery_type_update_validation()
    {
        $deliveryType = DeliveryType::factory()->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->patch(route('admin.api.update.delivery-type', [
            'deliveryType' => $deliveryType
        ]), [
            'name' => json_encode([
                'en' => 'Express overnight UPDATED',
                'de' => 'Express Ubernacht AKTUALISIERT'
            ]),
            'description' => json_encode([
                'en' => 'Very quick UPDATED',
                'de' => 'Sehr schnell AKTUALISIERT'
            ]),
            'price' => 'twenty',
            'status' => DeliveryTypeStatuses::Enabled
        ]);
        $response->assertStatus(422);
    }

}
