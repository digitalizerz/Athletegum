@extends('emails.layout')

@section('content')
<p>Hi {{ $firstName }},</p>

<p>We received a request to reset your AthleteGum password.</p>

<p>Click the button below to set a new password. This link will expire shortly for security.</p>

<p>
    <a href="{{ $resetUrl }}" style="display: inline-block; padding: 14px 28px; margin: 24px 0; background-color: #000000; color: #ffffff; text-decoration: none; border-radius: 4px; font-weight: 500; font-size: 16px;">Reset password</a>
</p>

<p>If you didn't request this, you can safely ignore this email.</p>
@endsection

