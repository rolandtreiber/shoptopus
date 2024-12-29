@extends('email.layout.email-layout')

@section('subject')
    Reset your password
@endsection

@section('hero-background-image', 'email-header.jpg')
@section('hero-title') Reset your password @endsection
@section('hero-subtitle') You have requested your password to be reset @endsection
@section('hero-button-url') {{ $resetUrl }} @endsection
@section('hero-button-text') Reset Password @endsection

@section('title')
    Hello {{ $user->name }}
@endsection

@section('content')
    <p>You have said you forgot your password and so requested it to be reset.</p>
    <p>If it wasn't you, please contact us on <a href="mailto:{{config('app.support_email')}}">{{config('app.support_email')}}</a></p>
    <p>Click on the following link to reset your password <a href="{{ $resetUrl }}">{{ $resetUrl }}</a></p>
    @include('email.layout.partials.signature')
@endsection
