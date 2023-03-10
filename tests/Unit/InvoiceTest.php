<?php

namespace Tests\Unit;

use App\Models\Product;
use Tests\InvoicesTestCase;

/**
 * @group invoices
 */
class InvoiceTest extends InvoicesTestCase
{
    /**
     * @test
     */
    public function test_invoice_gets_created()
    {
        $this->createPayment();
        $this->assertNotNull($this->invoice);
    }

    /**
     * @test
     */
    public function test_invoice_has_the_right_address()
    {
        $this->createPayment();
        $this->assertEquals($this->invoice->address->address_line_1, $this->order->address->address_line_1);
        $this->assertEquals($this->invoice->address->address_line_2, $this->order->address->address_line_2);
        $this->assertEquals($this->invoice->address->town, $this->order->address->town);
        $this->assertEquals($this->invoice->address->post_code, $this->order->address->post_code);
        $this->assertEquals($this->invoice->address->slug, $this->order->address->slug);
    }

    /**
     * @test
     */
    public function test_invoice_has_the_right_payment()
    {
        $this->createPayment();
        $this->assertEquals($this->invoice->payment->amount, $this->order->payments()->first()->amount);
        $this->assertEquals($this->invoice->payment->payment_ref, $this->order->payments()->first()->payment_ref);
        $this->assertEquals($this->invoice->payment->source->id, $this->order->payments()->first()->payment_source->id);
    }

    /**
     * @test
     */
    public function test_invoice_has_the_right_totals()
    {
        $this->createPayment();
        $this->assertEquals($this->invoice->totals->delivery, $this->order->delivery_cost);
        $this->assertEquals($this->invoice->totals->subtotal, $this->order->subtotal);
        $this->assertEquals($this->invoice->totals->total_payable, $this->order->total_price);
        $this->assertEquals($this->invoice->totals->applied_discount, $this->order->total_discount);
    }

    /**
     * @test
     */
    public function test_invoice_has_the_right_delivery_type()
    {
        $this->createPayment();
        $this->assertEquals($this->invoice->delivery_type->id, $this->order->delivery_type->id);
    }

    /**
     * @test
     */
    public function test_invoice_has_all_products()
    {
        $products = Product::factory()->count(3)->create();
        foreach ($products as $product) {
            $this->order->products()->attach($product->id);
        }
        $orderProductIds = $this->order->products->pluck('id')->toArray();
        $this->createPayment();
        foreach ($this->invoice->products as $invoiceProduct) {
            $this->assertContains($invoiceProduct['product_id'], $orderProductIds);
        }
    }
}
