<x-superadmin-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.deals.index') }}" class="btn btn-ghost btn-sm">
                    ← Back to Deals
                </a>
                <h2 class="font-semibold text-xl leading-tight">Deal Messages (Read-Only)</h2>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <!-- Deal Info Card -->
        <div class="card bg-base-100 shadow-sm mb-6">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold">{{ \App\Models\Deal::getDealTypes()[$deal->deal_type]['name'] ?? $deal->deal_type }}</h3>
                        <p class="text-sm text-base-content/60 mt-1">
                            @if($deal->user && $deal->athlete)
                                Between {{ $deal->user->business_name ?? $deal->user->name ?? 'Business' }} and {{ $deal->athlete->name ?? 'Athlete' }}
                            @else
                                Deal ID: {{ $deal->id }}
                            @endif
                        </p>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-base-content/60">Compensation</div>
                        <div class="text-lg font-semibold">${{ number_format($deal->compensation_amount, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages Thread -->
        <div class="card bg-base-100 shadow-sm mb-6">
            <div class="card-body">
                <h3 class="text-lg font-semibold mb-4">Conversation</h3>
                
                <div class="alert alert-info mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm">Super Admin view - Read-only for audit and compliance purposes.</span>
                </div>
                
                <div class="space-y-4 max-h-[500px] overflow-y-auto" id="messages-container">
                    @forelse($messages as $message)
                        <div class="flex items-start gap-3">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-xs font-medium text-base-content/60">
                                        @if($message->is_system_message)
                                            System
                                        @elseif($message->sender_type === 'athlete')
                                            {{ $message->athleteSender->name ?? 'Athlete' }}
                                        @elseif($message->sender_type === 'user')
                                            {{ $message->sender->business_name ?? $message->sender->name ?? 'Business' }}
                                        @else
                                            Unknown
                                        @endif
                                    </span>
                                    <span class="text-xs text-base-content/40">
                                        {{ $message->created_at->format('M j, Y g:i A') }}
                                    </span>
                                </div>
                                
                                @if($message->is_system_message)
                                    <div class="inline-block bg-base-200 border border-base-300 rounded-lg px-4 py-2 text-sm text-base-content/70 italic">
                                        {{ $message->content }}
                                    </div>
                                @else
                                    <div class="inline-block bg-base-200 rounded-lg px-4 py-2 text-sm max-w-md">
                                        @if($message->content)
                                            <div class="whitespace-pre-wrap">{{ $message->content }}</div>
                                        @endif
                                        
                                        @if($message->hasAttachment())
                                            <div class="mt-2 pt-2 border-t border-base-300">
                                                <a href="{{ $message->attachment_url }}" target="_blank" class="flex items-center gap-2 text-sm hover:underline">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                    </svg>
                                                    {{ $message->attachment_original_name }}
                                                    @if($message->attachment_size)
                                                        <span class="text-xs opacity-70">({{ number_format($message->attachment_size / 1024, 1) }} KB)</span>
                                                    @endif
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-base-content/60">
                            <p>No messages in this conversation.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-scroll to bottom on page load
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('messages-container');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        });
    </script>
</x-superadmin-dashboard-layout>

