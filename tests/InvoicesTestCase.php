<?php

namespace Tests;

use App\Models\DeliveryType;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentSource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

class InvoicesTestCase extends TestCase
{
    use RefreshDatabase;

    protected $order;

    protected $invoice;

    protected $payment;

    protected $token;

    public function setUp(): void
    {
        parent::setUp();

        Notification::fake();
        $this->order = Order::factory()->state([
            'delivery_type_id' => DeliveryType::factory(),
        ])->create();
    }

    protected function createPayment()
    {
        $this->payment = Payment::factory()->state([
            'payable_type' => Order::class,
            'payable_id' => $this->order->id,
            'payment_source_id' => PaymentSource::factory(),
        ])->create();
        $this->invoice = Invoice::where('order_id', $this->order->id)->first();
    }
}
