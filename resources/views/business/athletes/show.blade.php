<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('business.athletes.index') }}" class="btn btn-ghost btn-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Athletes
                </a>
                <h2 class="text-2xl font-semibold">Athlete Profile</h2>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <div class="flex flex-col md:flex-row gap-6">
                        <!-- Profile Photo -->
                        <div class="flex-shrink-0">
                            @if($athlete->profile_photo)
                                <img 
                                    src="{{ asset('storage/' . $athlete->profile_photo) }}" 
                                    alt="{{ $athlete->name }}"
                                    class="w-32 h-32 rounded-full object-cover"
                                />
                            @else
                                <div class="w-32 h-32 rounded-full bg-gray-200 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <!-- Profile Details -->
                        <div class="flex-1">
                            <h1 class="text-3xl font-bold mb-4">{{ $athlete->name }}</h1>
                            
                            <div class="space-y-3">
                                @if($athlete->sport)
                                    <div>
                                        <span class="font-semibold text-gray-700">Sport:</span>
                                        <span class="ml-2 text-gray-600">{{ $athlete->sport }}</span>
                                    </div>
                                @endif
                                
                                @if($athlete->school)
                                    <div>
                                        <span class="font-semibold text-gray-700">School:</span>
                                        <span class="ml-2 text-gray-600">{{ $athlete->school }}</span>
                                    </div>
                                @endif
                                
                                @php
                                    $user = Auth::user();
                                    $canContactAthlete = \App\Support\PlanFeatures::canUseFeature($user, 'contact_athlete');
                                @endphp
                                
                                @if($canContactAthlete && $athlete->email)
                                    <div>
                                        <span class="font-semibold text-gray-700">Email:</span>
                                        <span class="ml-2 text-gray-600">{{ $athlete->email }}</span>
                                    </div>
                                @elseif(!$canContactAthlete)
                                    <div>
                                        <span class="font-semibold text-gray-700">Email:</span>
                                        <div class="mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                            <p class="text-sm text-yellow-800 mb-2">
                                                Upgrade to contact this athlete
                                            </p>
                                            <button 
                                                onclick="document.getElementById('upgrade-modal-email').showModal()"
                                                class="inline-flex items-center px-4 py-2 bg-black border border-black rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black transition"
                                            >
                                                Upgrade to Pro
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <x-upgrade-modal 
                                        modalId="upgrade-modal-email"
                                        title="🔓 Contact Athletes with Pro"
                                        description="Upgrade to message athletes, create deals, and track performance."
                                        actionText="Upgrade to Pro"
                                    />
                                @endif
                            </div>

                            <!-- Start Deal CTA -->
                            <div class="mt-6">
                                @php
                                    $user = Auth::user();
                                    $canStartDeal = \App\Support\PlanFeatures::maxActiveDeals($user) === null; // Unlimited deals = Pro/Growth
                                @endphp
                                
                                @if($canStartDeal)
                                    <a 
                                        href="{{ route('deals.create', ['athlete_id' => $athlete->id]) }}" 
                                        class="inline-flex items-center px-6 py-3 bg-black border border-black rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black transition"
                                    >
                                        Start Deal
                                    </a>
                                @else
                                    <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                        <p class="text-sm text-yellow-800 mb-3">
                                            <strong>Deal creation requires Pro or Growth.</strong>
                                        </p>
                                        <a 
                                            href="{{ route('business.billing.index') }}" 
                                            class="inline-flex items-center px-4 py-2 bg-black border border-black rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black transition"
                                        >
                                            Upgrade
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

