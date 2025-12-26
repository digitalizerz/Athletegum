<x-athlete-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl leading-tight">Submit Deliverables</h2>
            <a href="{{ route('athlete.dashboard') }}" class="btn btn-ghost btn-sm">
                ← Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto">
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

        <!-- Deal Summary -->
        <div class="card bg-base-100 shadow-sm mb-6">
            <div class="card-body">
                <h3 class="text-lg font-semibold mb-4">Deal Summary</h3>
                
                {{-- Business Identity Section --}}
                <div class="mb-4 pb-4 border-b border-base-300">
                    <div class="flex items-start gap-3">
                        @if($deal->user->business_name)
                            <div class="flex-1">
                                <div class="text-sm text-base-content/60 mb-1">Business</div>
                                <div class="font-semibold text-base">{{ $deal->user->business_name }}</div>
                                @if($deal->user->business_information)
                                    <div class="text-xs text-base-content/60 mt-1 line-clamp-2">{{ Str::limit($deal->user->business_information, 100) }}</div>
                                @endif
                                @if($deal->deal_type === 'in_person_appearance' && ($deal->user->city || $deal->user->state))
                                    <div class="text-xs text-base-content/60 mt-1">
                                        📍 {{ $deal->user->city }}{{ $deal->user->city && $deal->user->state ? ', ' : '' }}{{ $deal->user->state }}
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="flex-1">
                                <div class="text-sm text-base-content/60 mb-1">Business</div>
                                <div class="font-semibold text-base">{{ $deal->user->name ?? 'Business' }}</div>
                                @if($deal->deal_type === 'in_person_appearance' && ($deal->user->city || $deal->user->state))
                                    <div class="text-xs text-base-content/60 mt-1">
                                        📍 {{ $deal->user->city }}{{ $deal->user->city && $deal->user->state ? ', ' : '' }}{{ $deal->user->state }}
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-base-content/60">Deal Type:</span>
                        <span class="font-medium ml-2">{{ \App\Models\Deal::getDealTypes()[$deal->deal_type]['name'] ?? $deal->deal_type }}</span>
                    </div>
                    <div>
                        <span class="text-base-content/60">Compensation:</span>
                        <span class="font-medium ml-2">${{ number_format($deal->compensation_amount, 2) }}</span>
                    </div>
                    <div>
                        <span class="text-base-content/60">Deadline:</span>
                        <span class="font-medium ml-2">
                            {{ $deal->deadline->format('M j, Y') }}
                            @if($deal->deadline_time)
                                @php
                                    try {
                                        $time = \Carbon\Carbon::createFromFormat('H:i:s', $deal->deadline_time)->format('g:i A');
                                    } catch (\Exception $e) {
                                        $time = \Carbon\Carbon::createFromFormat('H:i', $deal->deadline_time)->format('g:i A');
                                    }
                                @endphp
                                at {{ $time }}
                            @endif
                        </span>
                    </div>
                    <div>
                        <span class="text-base-content/60">Status:</span>
                        <span class="font-medium ml-2">
                            <span class="badge badge-info badge-sm">Active</span>
                        </span>
                    </div>
                </div>

                {{-- Escrow Trust Message --}}
                <div class="mt-4 pt-4 border-t border-base-300">
                    <div class="alert alert-success">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <div class="text-xs">
                            <p class="font-semibold mb-1">Funded & Protected via AthleteGum Escrow</p>
                            <p class="text-base-content/70">Your payment of ${{ number_format($deal->compensation_amount, 2) }} is securely held in escrow and will be released automatically upon approval of your completed work.</p>
                        </div>
                    </div>
                </div>

                @if($deal->notes)
                    <div class="mt-4 pt-4 border-t border-base-300">
                        <span class="text-base-content/60 text-sm font-medium">Instructions:</span>
                        <div class="mt-2 text-sm whitespace-pre-wrap">{{ $deal->notes }}</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Submit Form -->
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
                <h3 class="text-lg font-semibold mb-4">Submit Your Work</h3>
                
                <form method="POST" action="{{ route('athlete.deals.submit.store', $deal) }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-6">
                        <label class="label">
                            <span class="label-text font-semibold">Completion Notes <span class="text-error">*</span></span>
                        </label>
                        <textarea 
                            name="completion_notes" 
                            class="textarea textarea-bordered w-full h-32" 
                            placeholder="Describe what you completed, provide links to posts, or any other relevant information..."
                            required
                        >{{ old('completion_notes') }}</textarea>
                        <label class="label">
                            <span class="label-text-alt text-base-content/60">Required. Provide details about the completed work.</span>
                        </label>
                        @error('completion_notes')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="label">
                            <span class="label-text font-semibold">Deliverables (Files)</span>
                        </label>
                        <input 
                            type="file" 
                            name="deliverables[]" 
                            class="file-input file-input-bordered w-full" 
                            multiple
                            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.mp4,.mov,.avi"
                        >
                        <label class="label">
                            <span class="label-text-alt text-base-content/60">Optional. Upload screenshots, videos, documents, or other proof of completion. Max 10 files, 10MB each.</span>
                        </label>
                        @error('deliverables')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                        @error('deliverables.*')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <div class="alert alert-info mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="text-sm">
                            <p class="font-semibold mb-1">What happens next?</p>
                            <ul class="list-disc list-inside space-y-1 text-xs">
                                <li>Your submission will be reviewed by the business</li>
                                <li>Once approved, payment will be released from escrow</li>
                                <li>You'll receive a notification when the deal is approved</li>
                            </ul>
                        </div>
                    </div>

                    <div class="flex justify-between items-center pt-4 border-t border-base-300">
                        <a href="{{ route('athlete.dashboard') }}" class="btn btn-ghost">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Submit Deliverables
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-athlete-dashboard-layout>

