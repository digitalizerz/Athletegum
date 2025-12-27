@extends('emails.layout')

@section('content')
<p>Hi {{ $athleteName }},</p>

<p>The business has requested revisions for your submitted work.</p>

<p><strong>Feedback:</strong></p>
<p style="background-color: #f5f5f5; padding: 16px; border-left: 3px solid #000000; margin: 16px 0;">"{{ $revisionNotes }}"</p>

<p>Please update and resubmit your deliverables.</p>

<p>
    <a href="{{ $dealUrl }}" style="display: inline-block; padding: 14px 28px; margin: 24px 0; background-color: #000000; color: #ffffff; text-decoration: none; border-radius: 4px; font-weight: 500; font-size: 16px;">View deal</a>
</p>
@endsection

