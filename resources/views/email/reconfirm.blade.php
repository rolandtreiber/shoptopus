@extends('email.layout.email-layout')

@section('subject')
    Hello! I am the email subject!
@endsection

@section('hero-background-image', 'email-header.jpg')
@section('hero-title') Please confirm your email @endsection
@section('hero-subtitle') You have updated your email in your profile @endsection
@section('hero-button-url') {{env('APP_URL')}}/{{ $emailConfirmationLink }} @endsection
@section('hero-button-text') Confirm your email @endsection

@section('title')
    Hello {{ $userName }}
@endsection

@section('content')
    <p>Please confirm your new email!</p>
    <p>We need to know that your email address is valid as part of our services, you'll receive important updates regarding to your orders via email.</p>
    <p>Click on the link below to confirm your email:</p>
    <a href="{{ env('APP_URL')}} / {{ $emailConfirmationLink }}">Confirm my email</a>
    @include('email.layout.partials.signature')
@endsection
