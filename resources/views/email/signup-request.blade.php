@extends('email.layout.email-layout')

@section('subject')
    You've been invited!
@endsection

@section('hero-background-image', 'email-header.jpg')
@section('hero-title') Complete your signup @endsection
@section('hero-subtitle') You've been invited to work with {{env('APP_NAME')}} @endsection
@section('hero-button-url') {{env('APP_URL')}}/{{ $secureSignupUrl }} @endsection
@section('hero-button-text') Sign up @endsection

@section('title')
    Hello
@endsection

@section('content')
    <p>You've been requested to sign up by {{$user->name}}</p>
    <p>{{env('APP_NAME')}} asks you to join.</p>
    <p>Click on the link below to confirm your email:</p>
    <a href="{{ env('APP_URL')}}/{{ $secureSignupUrl }}">Sign up</a>
    @include('email.layout.partials.signature')
@endsection
