@extends('email.layout.email-layout')

@section('subject')
    Your account has been deactivated
@endsection

@section('hero-background-image', 'email-header-1.jpg')
@section('hero-title') Your account has been deactivated @endsection
@section('hero-subtitle') We're sorry to see you go :( @endsection
@section('hero-button-url') {{env('APP_URL')}} @endsection
@section('hero-button-text') Come back soon @endsection

@section('title')
    Hello {{ $originalFirstName }}
@endsection

@section('content')
    <p>You have requested your account to be deactivated.</p>
    <h2>Your account is now deactivated.</h2>
    @if($anonimize === false)
        <p>Your account is now 'soft' deleted from our system.</p>
        <p>This means that we keep audit data and if you'd like, we may be able to reinstate your account upon your request.</p>
        <p>To request this, please contact us on <a href="mailto:{{config('app.support_email')}}"></a></p>
    @else
        <p>Your account is now anonimised and 'soft' deleted.</p>
        <p>This means that we keep audit data however all your personal identifiable data has been replaced by random text. For your records, please find the values and replacements below.</p>
        <p></p>
        <table style="width: 100%">
            <tbody>
                <tr>
                    <td>Email "{{$originalEmail}}" was updated by "{{$emailReplacement}}"</td>
                </tr>
                <tr>
                    <td>First Name "{{$originalFirstName}}" was updated by "{{$firstNameReplacement}}"</td>
                </tr>
                <tr>
                    <td>Last Name "{{$originalLastName}}" was updated by "{{$lastNameReplacement}}"</td>
                </tr>
                <tr>
                    <td>Phone "{{$originalPhone}}" was updated by "{{$phoneReplacement}}"</td>
                </tr>
            </tbody>
        </table>
    @endif
    <p>Thank you. Alternatively if it wasn't you, please contact us on <a href="mailto:{{config('app.support_email')}}">{{config('app.support_email')}}</a></p>
    @include('email.layout.partials.signature')
@endsection
