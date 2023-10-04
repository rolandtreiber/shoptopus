<?php

namespace App\Repositories\Admin\Invoice;

use App\Enums\AccessTokenType;
use App\Enums\PaymentType;
use App\Models\AccessToken;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    public function create(Order $order): Invoice
    {
        // Clear out any existing in case re-generating invoice ever comes in scope
        $existing = Invoice::where('user_id', $order->user->id)->where('order_id', $order->id)->get();
        $existing->map(function ($invoice) {
            $invoice->delete();
        });

        $payment = $order->payments()->where('type', PaymentType::Payment)->first();
        $products = $order->products;
        $voucherCode = $order->voucher_code;
        $deliveryType = $order->delivery_type;

        // Creating a new invoice
        $invoice = new Invoice();
        $invoice->user_id = $order->user->id;
        $invoice->order_id = $order->id;
        $invoice->address = $order->address;
        $invoice->payment = [
            'amount' => $payment->amount,
            'created_at' => Carbon::parse($payment->created_at)->format('d-m-Y H:i'),
            'payment_ref' => $payment->payment_ref,
            'source' => $payment->payment_source,
        ];
        $invoice->products = $products->map(function ($product) {
            /** @var OrderProduct $pivot */
            // @phpstan-ignore-next-line
            $pivot = $product->pivot;

            /** @var Product $product */
            $result = [
                'id' => $pivot->id,
                'sku' => $product->sku,
                'name' => $pivot->name,
                'type' => $pivot->product_variant_id !== null ? 'product_variant' : 'product',
                'amount' => $pivot->amount,
                'urls' => $pivot->urls ? $pivot->urls : null,
                'full_price' => $pivot->full_price,
                'product_id' => $pivot->product_id,
                'unit_price' => $pivot->unit_price,
                'final_price' => $pivot->final_price,
                'unit_discount' => $pivot->unit_discount,
                'total_discount' => $pivot->total_discount,
                'original_unit_price' => $pivot->original_unit_price,
                'image' => $product->cover_photo ? $this->getImage($product->cover_photo->url) : null,
            ];
            $pivot->product_variant_id !== null && $result['product_variant_id'] = $pivot->product_variant_id;

            return $result;
        })->toArray();

        $invoice->voucher_code = $voucherCode;
        $invoice->delivery_type = $deliveryType;
        $invoice->totals = [
            'total_payable' => $order->total_price,
            'applied_discount' => $order->total_discount,
            'delivery' => $order->delivery_cost,
            'subtotal' => $order->subtotal,
        ];

        $invoice->save();

        $accessToken = new AccessToken();
        $accessToken->accessable_type = Invoice::class;
        $accessToken->accessable_id = $invoice->id;
        $accessToken->user_id = $order->user_id;
        $accessToken->issuer_user_id = $order->user_id;
        $accessToken->type = AccessTokenType::Invoice;
        $accessToken->save();

        return $invoice;
    }

    private function getImage($url)
    {
        if (env('APP_ENV') === 'local') {
            try {
                $contents = file_get_contents($url);
            } catch (\Exception $exception) {
                $contents = file_get_contents(str_replace(env('APP_URL'), __DIR__.'/../../../../public', $url));
            }
        } else {
            $contents = file_get_contents($url);
        }

        $img = Image::make($contents);
        $img->orientate();
        $fileName = Str::random(40).'.jpg';
        $data = $img->resize(50, 50, function ($const) {
            $const->aspectRatio();
        })->encode('jpg', 80);
        $data->extension = 'jpg';

        return base64_encode($data);
    }
}
