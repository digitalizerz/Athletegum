@extends('emails.layout')

@section('content')
<p>Hi {{ $firstName }},</p>

<p>Thanks for creating an AthleteGum account.</p>

<p>Please confirm your email address by clicking the button below.</p>

<p>
    <a href="{{ $verificationUrl }}" style="display: inline-block; padding: 14px 28px; margin: 24px 0; background-color: #000000; color: #ffffff; text-decoration: none; border-radius: 4px; font-weight: 500; font-size: 16px;">Verify email</a>
</p>

<p>If you didn't create this account, you can ignore this email.</p>
@endsection

