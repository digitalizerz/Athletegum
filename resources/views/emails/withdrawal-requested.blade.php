@extends('emails.layout')

@section('content')
<p>Hi {{ $athleteName }},</p>

<p>We've received your request to withdraw ${{ number_format($amount, 2) }}.</p>

<p>You'll be notified once the payout is processed.</p>

<p>
    <a href="{{ $earningsUrl }}" style="display: inline-block; padding: 14px 28px; margin: 24px 0; background-color: #000000; color: #ffffff; text-decoration: none; border-radius: 4px; font-weight: 500; font-size: 16px;">View earnings</a>
</p>
@endsection

