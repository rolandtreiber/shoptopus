@extends('email.layout.email-layout')

@section('subject')
    Order completed
@endsection

@section('hero-background-image', 'email-header.jpg')
@section('hero-title') Order completed @endsection
@section('hero-subtitle') Thank you for your purchase! @endsection
@section('hero-button-url') {{env('APP_URL')}} @endsection
@section('hero-button-text') Log in @endsection

@section('title')
    Hello {{ $user->name }}
@endsection

@section('content')
    <p>We are writing you with regards to your order <b>{{ $order->id }}</b>.</p>
    <h2>Order details:</h2>
    <table style="width: 100%">
        <tbody>
        @foreach($order->invoice->products as $product)
            <tr>
                <td style="width: 50px"><img src="data:image/jpg;base64,{{$product['image']}}" width="40" height="40"/></td>
                <td>{{$product['name']}}</td>
                <td style="float: right"><a href="{{config('app.frontend_url_public')}}/review/product/{{$product['id']}}?token={{$token->token}}">Leave a review</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <p>Thank you. Alternatively if it wasn't you, please contact us on <a href="mailto:{{config('app.support_email')}}">{{config('app.support_email')}}</a></p>
    <p>Access your account <a href="{{config('app.app_url')}}">here</a>.</p>
    @include('email.layout.partials.signature')
@endsection
