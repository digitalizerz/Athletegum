<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Deal Created
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-8 text-center">
                    <div class="mb-6">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 mb-4">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-semibold text-gray-900 mb-2">Deal created successfully</h3>
                        <p class="text-gray-600">Share this link with the athlete to get started.</p>
                    </div>

                    <div class="mb-6 sm:mb-8 bg-gray-50 rounded-lg p-4 sm:p-6">
                        <div class="text-sm sm:text-base text-gray-500 mb-3">
                            @if(isset($invitation) && $invitation)
                                Share this invitation link with the athlete
                                @if($invitation->athlete_email)
                                    <span class="block text-xs mt-1 text-gray-400">Intended for: {{ $invitation->athlete_email }}</span>
                                @endif
                            @else
                                Share this link with the athlete
                            @endif
                        </div>
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-2 bg-white border border-gray-300 rounded-md p-3">
                            <input 
                                type="text" 
                                id="deal-link" 
                                value="{{ isset($invitation) && $invitation ? route('deals.show.token', $invitation->token) : route('deals.show.token', $deal->token) }}" 
                                readonly
                                class="flex-1 border-0 focus:ring-0 text-sm sm:text-base text-gray-900 bg-transparent py-2 sm:py-0"
                            >
                            <button 
                                onclick="copyToClipboard()"
                                class="px-5 py-3 sm:py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-base sm:text-sm font-medium transition min-h-[44px] sm:min-h-0"
                            >
                                Copy Link
                            </button>
                        </div>
                        <div id="copied-message" class="hidden text-sm sm:text-base text-green-600 mt-2 font-medium">✓ Link copied!</div>
                        @if(isset($invitation) && $invitation && $invitation->athlete_email)
                            <p class="text-xs text-gray-500 mt-3">
                                <strong>Security:</strong> Only the athlete with email <strong>{{ $invitation->athlete_email }}</strong> can accept this deal.
                            </p>
                        @endif
                    </div>

                    <div class="flex flex-col sm:flex-row justify-center items-center gap-3 sm:gap-4 text-base sm:text-lg">
                        <a href="{{ route('deals.create') }}" class="text-gray-600 hover:text-gray-900 font-medium py-2 min-h-[44px] flex items-center">
                            Create another deal
                        </a>
                        <span class="text-gray-300 hidden sm:inline">|</span>
                        <a href="{{ route('dashboard') }}" class="text-indigo-600 hover:text-indigo-700 font-medium py-2 min-h-[44px] flex items-center">
                            Go to dashboard
                        </a>
                    </div>
                </div>
            </div>
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

