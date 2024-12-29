@extends('email.layout.email-layout')

@section('subject')
    Your email is now confirmed.
@endsection

@section('hero-background-image', 'email-header.jpg')
@section('hero-title') Your email is now confirmed @endsection
@section('hero-subtitle') Thank you for confirming your email address. Now we know it's yours. @endsection
@section('hero-button-url') {{env('APP_URL')}} @endsection
@section('hero-button-text') Log in @endsection

@section('title')
    Hello {{ $user->name }}
@endsection

@section('content')
    <p>To make sure your specified email address is safe to use, we asked you to confirm it, which you have successfully done.</p>
    <p>Thank you. Alternatively if it wasn't you, please contact us on <a href="mailto:{{config('app.support_email')}}">{{config('app.support_email')}}</a></p>
    <p>Access your account <a href="{{config('app.app_url')}}">here</a>.</p>
    @include('email.layout.partials.signature')
@endsection
