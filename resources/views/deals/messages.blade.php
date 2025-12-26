@php
use Illuminate\Support\Facades\Storage;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('deals.index') }}" class="btn btn-ghost btn-sm">
                    ← Back to Deals
                </a>
                <h2 class="font-semibold text-xl leading-tight">Deal Messages</h2>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        @if(session('success'))
            <div role="alert" class="alert alert-success mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div role="alert" class="alert alert-error mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <h3 class="font-bold">Error</h3>
                    <div class="text-xs">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Deal Info Card -->
        <div class="card bg-base-100 shadow-sm mb-6">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold">{{ \App\Models\Deal::getDealTypes()[$deal->deal_type]['name'] ?? $deal->deal_type }}</h3>
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
                        <div class="text-lg font-semibold">${{ number_format($deal->compensation_amount, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Deliverables Section -->
        @if($deal->completed_at && !empty($deal->deliverables))
            <div class="card bg-base-100 shadow-sm mb-6">
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

        <!-- Messages Thread -->
        <div class="card bg-base-100 shadow-sm mb-6">
            <div class="card-body">
                <h3 class="text-lg font-semibold mb-4">Conversation</h3>
                
                <div class="space-y-4 max-h-[500px] overflow-y-auto mb-6" id="messages-container">
                    @forelse($messages as $message)
                        <div class="flex items-start gap-3 {{ $message->sender_type === 'user' ? 'flex-row-reverse' : '' }}">
                            <div class="flex-1 {{ $message->sender_type === 'user' ? 'text-right' : '' }}">
                                <div class="flex items-center gap-2 mb-1 {{ $message->sender_type === 'user' ? 'justify-end' : '' }}">
                                    <span class="text-xs font-medium text-base-content/60">
                                        @if($message->is_system_message)
                                            System
                                        @elseif($message->sender_type === 'athlete')
                                            {{ $message->athleteSender->name ?? 'Athlete' }}
                                        @else
                                            You
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
                                    <div class="inline-block {{ $message->sender_type === 'user' ? 'bg-primary text-primary-content' : 'bg-base-200' }} rounded-lg px-4 py-2 text-sm max-w-md">
                                        @if($message->content)
                                            <div class="whitespace-pre-wrap">{{ $message->content }}</div>
                                        @endif
                                        
                                        @if($message->hasAttachment())
                                            <div class="mt-2 pt-2 border-t {{ $message->sender_type === 'user' ? 'border-primary-content/20' : 'border-base-300' }}">
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
                            <p>No messages yet. Start the conversation below.</p>
                        </div>
                    @endforelse
                </div>

                @if(in_array($deal->status, ['completed', 'paid']) && $deal->released_at)
                    <div class="alert alert-info">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm">This deal is completed. Messaging is read-only for audit purposes.</span>
                    </div>
                @else
                    <!-- Message Input -->
                    <form method="POST" action="{{ route('deals.messages.store', $deal) }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        
                        <!-- Guardrails Warning -->
                        <div class="alert alert-warning">
                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <div class="text-xs">
                                <p class="font-semibold mb-1">For your protection, keep all communication inside AthleteGum.</p>
                                <p class="text-base-content/70">Payments and escrow depend on it. Email addresses, phone numbers, and external links may be filtered.</p>
                            </div>
                        </div>

                        <div class="form-control">
                            <textarea 
                                name="content" 
                                class="textarea textarea-bordered w-full h-24" 
                                placeholder="Type your message..."
                                required
                            >{{ old('content') }}</textarea>
                            <x-input-error :messages="$errors->get('content')" class="mt-2" />
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text text-sm">Attachment (optional)</span>
                            </label>
                            <input 
                                type="file" 
                                name="attachment" 
                                class="file-input file-input-bordered w-full" 
                                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.mp4,.mov,.avi"
                            >
                            <label class="label">
                                <span class="label-text-alt text-base-content/60">Max 10MB. Images, PDFs, documents, or videos.</span>
                            </label>
                            <x-input-error :messages="$errors->get('attachment')" class="mt-2" />
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="btn btn-primary">
                                Send Message
                            </button>
                        </div>
                    </form>
                @endif
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
</x-app-layout>

