@extends('email.layout.email-layout')

@section('subject')
    Hello! I am the email subject!
@endsection

@section('hero-background-image', 'email-header.jpg')
@section('hero-title') Welcome to {{config('app.name')}} @endsection
@section('hero-subtitle') Click on the button to confirm your email @endsection
@section('hero-button-url'){{ $emailConfirmationLink }}@endsection
@section('hero-button-text') Confirm your email @endsection

@section('title')
    Hello {{ $userName }}
@endsection

@section('content')
    <p>Thank you for registering on our website!</p>
    <p>We need to know that your email address is valid as part of our services, you'll receive important updates regarding to your orders via email.</p>
    <p>Click on the link below to confirm your email:</p>
    <a href="{{ $emailConfirmationLink }}">Confirm my email</a>
    @include('email.layout.partials.signature')
@endsection
