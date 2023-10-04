@extends('email.layout.email-layout')

@section('subject')
    Your files are ready to download
@endsection

@section('hero-background-image', 'email-header-1.jpg')
@section('hero-title') Your order is ready @endsection
@section('hero-subtitle') Download your files @endsection
@section('hero-button-url') {{env('APP_URL')}} @endsection
@section('hero-button-text') Log in @endsection

@section('title')
    Hello {{ $user->name }}
@endsection

@section('content')
    <p>Your order contains file assets.</p>
    <h2>Order details:</h2>
    <table style="width: 100%">
        <tbody>
        @foreach($order->invoice->products as $product)
            @if($product['urls'] !== null)
            <tr>
                <td style="width: 50px"><img src="data:image/jpg;base64,{{$product['image']}}" width="40" height="40"/></td>
                <td>{{$product['name']}}</td>
                <td style="float: right">
                    <ul>
                        @foreach($product['urls'] as $url)
                            <li><a href="{{$url}}">{{$url}}</a></li>
                        @endforeach
                    </ul>
                </td>
            </tr>
            @endif
        @endforeach
        </tbody>
    </table>
    <p>Thank you. If you did not expect this email, please contact us on <a href="mailto:{{config('app.support_email')}}">{{config('app.support_email')}}</a></p>
    <p>Access your account <a href="{{config('app.app_url')}}">here</a>.</p>
    @include('email.layout.partials.signature')
@endsection
