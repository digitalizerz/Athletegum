@extends('emails.layout')

@section('content')
<p>Hi {{ $athleteName }},</p>

<p>A business has created a new deal for you on AthleteGum.</p>

<p>
    <strong>Deal:</strong> {{ \App\Models\Deal::getDealTypes()[$deal->deal_type]['name'] ?? $deal->deal_type }}<br>
    <strong>Payment:</strong> ${{ number_format($deal->compensation_amount, 2) }}
</p>

<p>Log in to review the details and accept the deal.</p>

<p>
    <a href="{{ $dealUrl }}" style="display: inline-block; padding: 14px 28px; margin: 24px 0; background-color: #000000; color: #ffffff; text-decoration: none; border-radius: 4px; font-weight: 500; font-size: 16px;">View deal</a>
</p>
@endsection

