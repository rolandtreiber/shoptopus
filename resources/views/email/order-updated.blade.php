@extends('email.layout.email-layout')

@section('subject')
    Your order status is now {{ \App\Enums\OrderStatus::getKey($order->status) }}
@endsection

@section('hero-background-image', 'email-header.jpg')
@section('hero-title') Order Status Updated @endsection
@section('hero-subtitle') Your feedback matters! @endsection
@section('hero-button-url') {{env('APP_URL')}} @endsection
@section('hero-button-text') Log in @endsection

@section('title')
    Hello {{ $order->user->name }}
@endsection

@section('content')
    <p>We are writing to let you know that your order's status was updated to {{ \App\Enums\OrderStatus::getKey($order->status) }}</p>
    <h2>Order details</h2>
    <table>
        <thead>
            <tr>
                <td>ID</td>
                <td>{{$order->id}}</td>
            </tr>
            <tr>
                <td>Slug</td>
                <td>{{$order->slug}}</td>
            </tr>
            <tr>
                <td>Placed at</td>
                <td>{{$order->created_at->format('Y-m-d H:i')}}</td>
            </tr>
        </thead>
    </table>
    <p>Thank you. Alternatively if it wasn't you, please contact us on <a href="mailto:{{config('app.support_email')}}">{{config('app.support_email')}}</a></p>
    <p>Access your account <a href="{{config('app.app_url')}}">here</a>.</p>
    @include('email.layout.partials.signature')
@endsection
