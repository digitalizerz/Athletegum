<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">Messages</h2>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        @if($deals->count() > 0)
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body p-0">
                    <div class="divide-y divide-base-300">
                        @foreach($deals as $deal)
                            @php
                                $latestMessage = $deal->messages->first();
                            @endphp
                            <a href="{{ route('deals.messages', $deal) }}" class="block p-4 hover:bg-base-200 transition">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <h3 class="font-semibold">{{ \App\Models\Deal::getDealTypes()[$deal->deal_type]['name'] ?? $deal->deal_type }}</h3>
                                        </div>
                                        <p class="text-sm text-base-content/60 mb-1">
                                            @if($deal->athlete)
                                                With {{ $deal->athlete->name }}
                                            @else
                                                Awaiting athlete
                                            @endif
                                        </p>
                                        @if($latestMessage)
                                            <p class="text-sm text-base-content/70 truncate">
                                                @if($latestMessage->is_system_message)
                                                    <span class="italic">{{ $latestMessage->content }}</span>
                                                @else
                                                    <strong>{{ $latestMessage->sender_name }}:</strong> {{ Str::limit($latestMessage->content ?: 'Sent an attachment', 60) }}
                                                @endif
                                            </p>
                                            <p class="text-xs text-base-content/50 mt-1">{{ $latestMessage->created_at->diffForHumans() }}</p>
                                        @endif
                                    </div>
                                    <svg class="w-5 h-5 text-base-content/40 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
                
                @if($deals->hasPages())
                    <div class="card-body border-t border-base-300">
                        {{ $deals->links() }}
                    </div>
                @endif
            </div>
        @else
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-base-content/30 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <p class="text-base-content/60 mb-2">No messages yet</p>
                    <p class="text-sm text-base-content/50">Start a conversation by accepting a deal or creating one.</p>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>

