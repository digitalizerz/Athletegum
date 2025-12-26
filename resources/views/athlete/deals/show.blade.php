<x-athlete-dashboard-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Deal Details
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto space-y-6">
            <!-- Deal Summary Card -->
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <div class="flex items-start justify-between mb-6">
                        <div>
                            <h1 class="text-2xl font-bold mb-2">
                                {{ \App\Models\Deal::getDealTypes()[$deal->deal_type]['name'] ?? ucfirst(str_replace('_', ' ', $deal->deal_type)) }}
                            </h1>
                            <div class="flex items-center gap-3 mt-2">
                                @php
                                    $statusColors = [
                                        'pending' => 'badge-warning',
                                        'accepted' => 'badge-info',
                                        'completed' => 'badge-success',
                                        'cancelled' => 'badge-error',
                                    ];
                                    $statusColor = $statusColors[$deal->status] ?? 'badge-ghost';
                                @endphp
                                <span class="badge {{ $statusColor }}">{{ ucfirst($deal->status) }}</span>
                                @if($deal->payment_status === 'paid')
                                    <span class="badge badge-success">Payment Received</span>
                                @endif
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-base-content/60">Compensation</div>
                            <div class="text-2xl font-bold text-success">${{ number_format($deal->compensation_amount, 2) }}</div>
                        </div>
                    </div>

                    <!-- Deal Information Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="text-sm font-semibold text-base-content/60 mb-2">Deadline</div>
                            <div class="text-lg">
                                {{ $deal->deadline->format('M d, Y') }}
                                @if($deal->deadline_time)
                                    at {{ \Carbon\Carbon::parse($deal->deadline_time)->format('g:i A') }}
                                @endif
                            </div>
                            @if($deal->deadline->isPast() && $deal->status !== 'completed')
                                <div class="text-xs text-error mt-1">Overdue</div>
                            @elseif($deal->deadline->isToday())
                                <div class="text-xs text-warning mt-1">Due today</div>
                            @else
                                <div class="text-xs text-base-content/60 mt-1">
                                    {{ $deal->deadline->diffForHumans() }}
                                </div>
                            @endif
                        </div>

                        @if(!empty($deal->platforms) && is_array($deal->platforms))
                        <div>
                            <div class="text-sm font-semibold text-base-content/60 mb-2">Platforms</div>
                            <div class="flex flex-wrap gap-2">
                                @foreach($deal->platforms as $platform)
                                    <span class="badge badge-outline">{{ ucfirst($platform) }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if($deal->frequency)
                        <div>
                            <div class="text-sm font-semibold text-base-content/60 mb-2">Frequency</div>
                            <div>{{ \App\Models\Deal::getFrequencyOptions()[$deal->frequency] ?? $deal->frequency }}</div>
                        </div>
                        @endif

                        <div>
                            <div class="text-sm font-semibold text-base-content/60 mb-2">Created</div>
                            <div>{{ $deal->created_at->format('M d, Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Instructions Section -->
            @if($deal->notes)
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h3 class="text-lg font-semibold mb-4">Instructions</h3>
                    <div class="prose max-w-none whitespace-pre-wrap">{{ $deal->notes }}</div>
                </div>
            </div>
            @endif

            <!-- Attachments Section -->
            @if(!empty($deal->attachments) && is_array($deal->attachments))
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h3 class="text-lg font-semibold mb-4">Attachments</h3>
                    <div class="space-y-2">
                        @foreach($deal->attachments as $attachment)
                            <div class="flex items-center justify-between p-3 bg-base-200 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-base-content/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span class="text-sm">{{ $attachment['original_name'] ?? basename($attachment['path'] ?? '') }}</span>
                                </div>
                                @if(isset($attachment['path']))
                                    <a href="{{ Storage::url($attachment['path']) }}" target="_blank" class="btn btn-ghost btn-sm">
                                        Download
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Contract Section -->
            @if($deal->contract_text)
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h3 class="text-lg font-semibold mb-4">Contract Agreement</h3>
                    <div class="bg-base-200 border border-base-300 rounded-lg p-4 max-h-64 overflow-y-auto mb-4">
                        <div class="prose max-w-none text-sm whitespace-pre-wrap">{{ $deal->contract_text }}</div>
                    </div>
                    @if($deal->contract_signed)
                        <div class="flex items-center gap-2 text-success">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Signed on {{ $deal->contract_signed_at->format('M d, Y \a\t g:i A') }}</span>
                        </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Completion Status -->
            @if($deal->completed_at)
            <div class="card bg-base-100 shadow-sm border-l-4 border-success">
                <div class="card-body">
                    <h3 class="text-lg font-semibold mb-4">Completion Details</h3>
                    <div class="space-y-3">
                        <div>
                            <div class="text-sm font-semibold text-base-content/60 mb-1">Completed On</div>
                            <div>{{ $deal->completed_at->format('M d, Y \a\t g:i A') }}</div>
                        </div>
                        @if($deal->completion_notes)
                        <div>
                            <div class="text-sm font-semibold text-base-content/60 mb-1">Completion Notes</div>
                            <div class="whitespace-pre-wrap">{{ $deal->completion_notes }}</div>
                        </div>
                        @endif
                        @if($deal->is_approved)
                            <div class="badge badge-success">Approved by Business</div>
                        @elseif($deal->status === 'completed')
                            <div class="badge badge-warning">Awaiting Business Approval</div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('athlete.deals.index') }}" class="btn btn-ghost">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Deals
                </a>

                @if($deal->status === 'accepted' && !$deal->completed_at && !$deal->released_at)
                    <a href="{{ route('athlete.deals.submit.show', $deal) }}" class="btn btn-primary">
                        Submit Deliverables
                    </a>
                @endif

                @if($deal->status === 'accepted' && !$deal->completed_at && !$deal->released_at)
                    <a href="{{ route('athlete.deals.cancel', $deal) }}" class="btn btn-outline btn-error">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Cancel Deal
                    </a>
                @endif

                @if($deal->status === 'accepted' || $deal->status === 'completed')
                    <a href="{{ route('athlete.deals.messages', $deal) }}" class="btn btn-outline">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        Messages
                    </a>
                @endif
            </div>
        </div>
    </div>
</x-athlete-dashboard-layout>

