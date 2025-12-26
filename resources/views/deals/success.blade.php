@php
use Illuminate\Support\Facades\Storage;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Deal Details
            </h2>
            <a href="{{ route('deals.index') }}" class="btn btn-ghost btn-sm">
                ← Back to Deals
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Deal Summary Card -->
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h1 class="text-2xl font-bold mb-2">
                                {{ \App\Models\Deal::getDealTypes()[$deal->deal_type]['name'] ?? ucfirst(str_replace('_', ' ', $deal->deal_type)) }}
                            </h1>
                            <p class="text-sm text-base-content/60 mt-1">
                                @if($deal->athlete)
                                    With {{ $deal->athlete->name }}
                                @else
                                    Awaiting athlete acceptance
                                @endif
                            </p>
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
                        </div>

                        <div>
                            <div class="text-sm font-semibold text-base-content/60 mb-2">Status</div>
                            <div>
                                @php
                                    $statusBadges = [
                                        'draft' => 'badge-ghost',
                                        'pending' => 'badge-warning',
                                        'accepted' => 'badge-info',
                                        'completed' => 'badge-success',
                                        'cancelled' => 'badge-error',
                                    ];
                                    $statusBadge = $statusBadges[$deal->status] ?? 'badge-ghost';
                                @endphp
                                <span class="badge {{ $statusBadge }}">{{ ucfirst($deal->status) }}</span>
                            </div>
                        </div>

                        <div>
                            <div class="text-sm font-semibold text-base-content/60 mb-2">Created</div>
                            <div>{{ $deal->created_at->format('M d, Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Deliverables Section -->
            @if($deal->completed_at && !empty($deal->deliverables))
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body">
                        <h3 class="text-lg font-semibold mb-4">Submitted Deliverables</h3>
                        
                        @if($deal->completion_notes)
                            <div class="mb-4">
                                <div class="text-sm font-semibold text-base-content/60 mb-2">Completion Notes</div>
                                <div class="bg-base-200 rounded-lg p-4 whitespace-pre-wrap text-sm">{{ $deal->completion_notes }}</div>
                            </div>
                        @endif

                        <div class="space-y-3">
                            @foreach($deal->deliverables as $deliverable)
                                <div class="flex items-center justify-between p-3 bg-base-200 rounded-lg border border-base-300">
                                    <div class="flex items-center gap-3 flex-1 min-w-0">
                                        <svg class="w-5 h-5 text-base-content/60 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm font-medium truncate">{{ $deliverable['original_name'] ?? basename($deliverable['path'] ?? '') }}</div>
                                            @if(isset($deliverable['size']))
                                                <div class="text-xs text-base-content/60">{{ number_format($deliverable['size'] / 1024, 1) }} KB</div>
                                            @endif
                                        </div>
                                    </div>
                                    @if(isset($deliverable['path']))
                                        <a href="{{ Storage::url($deliverable['path']) }}" target="_blank" class="btn btn-ghost btn-sm ml-3 flex-shrink-0">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                            Download
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        @if($deal->completed_at)
                            <div class="mt-4 pt-4 border-t border-base-300 text-xs text-base-content/60">
                                Submitted on {{ $deal->completed_at->format('M j, Y \a\t g:i A') }}
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            @if(!$deal->athlete_id || $deal->status === 'pending')
            <!-- Original Success Message (only show if deal not yet accepted) -->
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body text-center">
                    <div class="mb-6">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-success/20 mb-4">
                            <svg class="w-8 h-8 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-semibold mb-2">Deal created successfully</h3>
                        <p class="text-base-content/60">Share this link with the athlete to get started.</p>
                    </div>

                    @php
                        $invitation = $deal->invitations()->where('status', 'pending')->first();
                    @endphp
                    <div class="mb-6 bg-base-200 rounded-lg p-4 sm:p-6">
                        <div class="text-sm text-base-content/70 mb-3">
                            @if($invitation)
                                Share this invitation link with the athlete
                                @if($invitation->athlete_email)
                                    <span class="block text-xs mt-1 text-base-content/50">Intended for: {{ $invitation->athlete_email }}</span>
                                @endif
                            @else
                                Share this link with the athlete
                            @endif
                        </div>
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 bg-base-100 border border-base-300 rounded-md p-3">
                            <input 
                                type="text" 
                                id="deal-link" 
                                value="{{ $invitation ? route('deals.show.token', $invitation->token) : route('deals.show.token', $deal->token) }}" 
                                readonly
                                class="flex-1 border-0 focus:ring-0 text-sm bg-transparent py-2"
                            >
                            <button 
                                onclick="copyToClipboard()"
                                class="btn btn-primary"
                            >
                                Copy Link
                            </button>
                        </div>
                        <div id="copied-message" class="hidden text-sm text-success mt-2 font-medium">✓ Link copied!</div>
                        @if($invitation && $invitation->athlete_email)
                            <p class="text-xs text-base-content/60 mt-3">
                                <strong>Security:</strong> Only the athlete with email <strong>{{ $invitation->athlete_email }}</strong> can accept this deal.
                            </p>
                        @endif
                    </div>

                    <div class="flex flex-col sm:flex-row justify-center items-center gap-3 sm:gap-4">
                        <a href="{{ route('deals.create') }}" class="btn btn-ghost">
                            Create another deal
                        </a>
                        <span class="text-base-content/30 hidden sm:inline">|</span>
                        <a href="{{ route('dashboard') }}" class="btn btn-primary">
                            Go to dashboard
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Action Buttons -->
            @if($deal->athlete_id && $deal->status !== 'pending' && $deal->status !== 'draft')
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body">
                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('deals.messages', $deal) }}" class="btn btn-primary">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                View Messages
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        async function copyToClipboard() {
            const linkInput = document.getElementById('deal-link');
            const link = linkInput.value;
            
            try {
                await navigator.clipboard.writeText(link);
            } catch (err) {
                // Fallback for older browsers
                linkInput.select();
                linkInput.setSelectionRange(0, 99999);
                document.execCommand('copy');
            }
            
            const message = document.getElementById('copied-message');
            message.classList.remove('hidden');
            setTimeout(() => {
                message.classList.add('hidden');
            }, 3000);
        }
    </script>
</x-app-layout>
