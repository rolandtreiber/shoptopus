<?php

namespace App\Console\Commands;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Payment;
use App\Models\PaymentSource;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixStoreDataExportHelper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fix-store-data-export-helper';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * @return array
     */
    private function orders(): array
    {
        $orders = Order::all();
        $data = [];
        foreach ($orders as $order) {
            $extract = [...$order->toArray()];
            unset($extract['created_at']);
            unset($extract['deleted_at']);
            unset($extract['updated_at']);
            unset($extract['id']);
            $extract['address_id'] = $order->address->slug;
            $extract['user_id'] = $order->user->slug;
            $extract['delivery_type_id'] = $order->delivery_type->slug;
            $extract['voucher_code_id'] = $order->voucher_code?->slug;
            $data[] = $extract;
        }
        return $data;
    }

    /**
     * @return array
     */
    private function orderProducts(): array
    {
        $orderProducts = OrderProduct::all();
        $data = [];
        foreach ($orderProducts as $orderProduct) {
            $extract = [...$orderProduct->toArray()];
            unset($extract['created_at']);
            unset($extract['deleted_at']);
            unset($extract['updated_at']);
            unset($extract['id']);
            $extract['order_id'] = $orderProduct->order->slug;
            $extract['product_id'] = $orderProduct->product->slug;
            $extract['product_variant_id'] = $orderProduct->productVariant?->slug;
            $data[] = $extract;
        }
        return $data;
    }

    /**
     * @return array
     */
    private function paymentSources(): array
    {
        $paymentSources = PaymentSource::all();
        $data = [];
        foreach ($paymentSources as $paymentSource) {
            $extract = [...$paymentSource->toArray()];
            unset($extract['created_at']);
            unset($extract['deleted_at']);
            unset($extract['updated_at']);
            unset($extract['id']);
            $extract['user_id'] = $paymentSource->user->slug;
            $data[] = $extract;
        }
        return $data;
    }

    /**
     * @return array
     */
    private function payments(): array
    {
        $payments = Payment::all();
        $data = [];
        foreach ($payments as $payment) {
            $extract = [...$payment->toArray()];
            unset($extract['created_at']);
            unset($extract['deleted_at']);
            unset($extract['updated_at']);
            unset($extract['id']);
            $extract['payable_id'] = $payment->payable->slug;
            $extract['payment_source_id'] = $payment->payment_source->slug;
            $extract['user_id'] = $payment->user->slug;
            $data[] = $extract;
        }
        return $data;
    }

    /**
     * @return array
     */
    private function addresses(): array
    {
        $payments = Address::all();
        $data = [];
        foreach ($payments as $payment) {
            $extract = [...$payment->toArray()];
            unset($extract['created_at']);
            unset($extract['deleted_at']);
            unset($extract['updated_at']);
            unset($extract['id']);
            unset($extract['google_maps_url']);
            $extract['user_id'] = $payment->user->slug;
            $data[] = $extract;
        }
        return $data;
    }

    /**
     * @return array
     */
    private function carts(): array
    {
        $carts = Cart::all();
        $data = [];
        foreach ($carts as $cart) {
            $extract = [...$cart->toArray()];
            unset($extract['created_at']);
            unset($extract['deleted_at']);
            unset($extract['updated_at']);
            unset($extract['id']);
            $extract['user_id'] = $cart->user->slug;
            $data[] = $extract;
        }
        return $data;
    }

    /**
     * @return array
     */
    private function cartProducts(): array
    {
        $cartProducts = DB::table('cart_product')->get();
        $data = [];
        foreach ($cartProducts as $cartProduct) {
            $data[] = [
                'cart_id' => Cart::find($cartProduct['cart_id'])->slug,
                'product_id' => Product::find($cartProduct['product_id'])->slug,
                'product_variant_id' => ProductVariant::find($cartProduct['product_variant_id'])?->slug,
                'quantity' => $cartProduct['quantity']
            ];
        }
        return $data;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $model = $this->choice(
            'What model would you like to see?',
            [
                "orders",
                "order-products",
                "payment-sources",
                "payments",
                "addresses",
                "carts",
                "cart-products"
            ],
            0
        );

        switch ($model) {
            case "orders":
                $this->line(json_encode($this->orders()));
                break;
            case "order-products":
                $this->line(json_encode($this->orderProducts()));
                break;
            case "payment-sources":
                $this->line(json_encode($this->paymentSources()));
                break;
            case "payments":
                $this->line(json_encode($this->payments()));
                break;
            case "addresses":
                $this->line(json_encode($this->addresses()));
                break;
            case "carts":
                $this->line(json_encode($this->carts()));
                break;
            case "cart-products":
                $this->line(json_encode($this->cartProducts()));
                break;
        }
        return 0;
    }
}
