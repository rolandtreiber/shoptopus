@extends('email.layout.email-layout')

@section('subject')
    Please leave a review of your order
@endsection

@section('hero-background-image', 'email-header.jpg')
@section('hero-title') Order created @endsection
@section('hero-subtitle') Thank you for shopping with us! Your order is in! @endsection
@section('hero-button-url') {{config('app.frontend_url_public')}} @endsection
@section('hero-button-text') Log in @endsection

@section('title')
    Hello {{ $user->name }}
@endsection

@section('content')
    <h2>Order details</h2>
    @if(!$invoice->order->hasOnlyVirtualProducts())
    <h3>Delivery Address</h3>
    <p>
        <a href="{{$invoice->google_maps_url}}">{{ $invoice->address->name }}</a>
    </p>
    @endif
    <h3>Products</h3>
    <table style="width: 100%">
        <tbody>
        @foreach($invoice->products as $product)
            <tr>
                <td><img src="data:image/jpg;base64,{{$product['image']}}" width="20" height="20"/></td>
                <td>{{$product['name']}}</td>
                <td>{{$product['sku']}}</td>
                <td>{{$product['amount']}}</td>
                @if(config('app.default_currency')['side'] === "left")
                    <td>{{config('app.default_currency')['symbol']}}{{$product['unit_price']}}</td>
                @else
                    <td>{{$product['unit_price']}}{{config('app.default_currency')['symbol']}}</td>
                @endif
                @if(config('app.default_currency')['side'] === "left")
                    <td>{{config('app.default_currency')['symbol']}}{{$product['final_price']}}</td>
                @else
                    <td>{{$product['final_price']}}{{config('app.default_currency')['symbol']}}</td>
                @endif
            </tr>
        @endforeach
        <tr>
            <td colspan="6" style="border-bottom: 1px dotted black"></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>Subtotal</td>
            <td>£{{$invoice->totals->subtotal}}</td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>Delivery</td>
            <td>£{{$invoice->totals->delivery}}</td>
        </tr>
        @if ($invoice->totals->applied_discount > 0)
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>Discount</td>
                <td>£{{$invoice->totals->applied_discount}}</td>
            </tr>
        @endif
        <tr>
            <td colspan="6" style="border-bottom: 1px dotted black"></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td style="font-size: 14px">Total</td>
            <td style="font-size: 14px">£{{$invoice->totals->total_payable}}</td>
        </tr>
        </tbody>
    </table>
    <p>Download your invoice in PDF format <a href="{{config('app.url')}}/invoice/{{$token->token}}">here</a>.</p>
    <p>Thank you. Alternatively if it wasn't you, please contact us on <a href="mailto:{{config('app.support_email')}}">{{config('app.support_email')}}</a></p>
    <p>Access your account <a href="{{config('app.frontend_url_public')}}/login">here</a>.</p>
    @include('email.layout.partials.signature')
@endsection
