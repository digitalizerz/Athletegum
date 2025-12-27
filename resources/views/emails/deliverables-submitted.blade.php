@extends('emails.layout')

@section('content')
<p>Hi {{ $businessName }},</p>

<p>{{ $athleteName }} has submitted deliverables for the following deal:</p>

<p>
    <strong>Deal:</strong> {{ \App\Models\Deal::getDealTypes()[$deal->deal_type]['name'] ?? $deal->deal_type }}
</p>

<p>You can now review the work and either approve it or request revisions.</p>

<p>
    <a href="{{ $dealUrl }}" style="display: inline-block; padding: 14px 28px; margin: 24px 0; background-color: #000000; color: #ffffff; text-decoration: none; border-radius: 4px; font-weight: 500; font-size: 16px;">Review deliverables</a>
</p>
@endsection

