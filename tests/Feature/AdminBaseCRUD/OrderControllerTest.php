<?php

namespace Tests\Feature\AdminBaseCRUD;

use App\Enums\PaymentType;
use App\Models\DeliveryType;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\AdminControllerTestCase;

/**
 * @group orders
 */
class OrderControllerTest extends AdminControllerTestCase
{
    use RefreshDatabase;

    /**
     * When deleting the delivery type, orders that have already been placed, should be returning the snapshot saved in their invoices
     * as it is the only reliable source of the delivery type selected for the order.
     *
     * @test
     */
    public function test_order_delivery_type_returned_after_deleting_delivery_type(): void
    {
        $deliveryType = DeliveryType::factory()->create();
        $deliveryTypeName = $deliveryType->getTranslations('name');
        $order = Order::factory()->state([
            'delivery_type_id' => $deliveryType->id,
        ])->create();
        Payment::factory()->state([
            'payable_type' => Order::class,
            'payable_id' => $order->id,
            'type' => PaymentType::Payment,
        ])->create();
        $deliveryType->delete();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->get(route('admin.api.index.orders', [
            'page' => 1,
            'paginate' => 20,
            'filters' => [],
        ]));
        $response->assertJsonFragment([
            'delivery_type' => $deliveryTypeName,
        ]);
    }
}
