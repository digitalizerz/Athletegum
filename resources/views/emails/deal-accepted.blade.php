@extends('emails.layout')

@section('content')
    <p style="margin: 0 0 16px; color: #111827; font-size: 16px; line-height: 1.5;">
        Hi {{ $businessName }},
    </p>

    <p style="margin: 0 0 16px; color: #111827; font-size: 16px; line-height: 1.5;">
        {{ $athleteName }} has accepted your deal.
    </p>

    <p style="margin: 0 0 16px; color: #111827; font-size: 16px; line-height: 1.5;">
        <strong>Deal:</strong> {{ $deal->deal_type }}<br>
        <strong>Payment:</strong> ${{ number_format($deal->compensation_amount, 2) }}<br>
        <strong>Deadline:</strong> {{ $deal->deadline->format('F j, Y') }}
    </p>

    <p style="margin: 0 0 24px; color: #111827; font-size: 16px; line-height: 1.5;">
        The athlete can now submit deliverables. You'll be notified when work is submitted for review.
    </p>

    <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin: 0 0 24px;">
        <tr>
            <td style="background-color: #111827; border-radius: 6px;">
                <a href="{{ $dealUrl }}" style="display: inline-block; padding: 12px 24px; color: #ffffff; text-decoration: none; font-weight: 600; font-size: 16px; border-radius: 6px;">
                    View deal
                </a>
            </td>
        </tr>
    </table>

    <p style="margin: 0; color: #6b7280; font-size: 14px; line-height: 1.5;">
        <a href="{{ $dealUrl }}" style="color: #111827; text-decoration: underline;">{{ $dealUrl }}</a>
    </p>
@endsection

