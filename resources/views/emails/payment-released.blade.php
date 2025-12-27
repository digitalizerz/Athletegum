@extends('emails.layout')

@section('content')
<p>Hi {{ $athleteName }},</p>

<p>Your work has been approved for the following deal:</p>

<p>
    <strong>Deal:</strong> {{ \App\Models\Deal::getDealTypes()[$deal->deal_type]['name'] ?? $deal->deal_type }}<br>
    <strong>Amount:</strong> ${{ number_format($payoutAmount, 2) }}
</p>

<p>Your earnings are now available according to your payout settings.</p>

<p>
    <a href="{{ $earningsUrl }}" style="display: inline-block; padding: 14px 28px; margin: 24px 0; background-color: #000000; color: #ffffff; text-decoration: none; border-radius: 4px; font-weight: 500; font-size: 16px;">View earnings</a>
</p>
@endsection

