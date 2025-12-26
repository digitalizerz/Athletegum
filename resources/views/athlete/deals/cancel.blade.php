<x-athlete-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl leading-tight">Cancel Deal</h2>
            <a href="{{ route('athlete.deals.show', $deal) }}" class="btn btn-ghost btn-sm">
                ← Back to Deal
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
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-base-content/60">Deal Type:</span>
                        <span class="font-medium ml-2">{{ \App\Models\Deal::getDealTypes()[$deal->deal_type]['name'] ?? $deal->deal_type }}</span>
                    </div>
                    <div>
                        <span class="text-base-content/60">Compensation:</span>
                        <span class="font-medium ml-2 text-success">${{ number_format($deal->compensation_amount, 2) }}</span>
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
                        <span class="badge badge-info ml-2">{{ ucfirst($deal->status) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cancellation Form -->
        <div class="card bg-base-100 shadow-sm border-l-4 border-warning">
            <div class="card-body">
                <div class="alert alert-warning mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                        <h3 class="font-bold">Important: Cancellation Notice</h3>
                        <div class="text-sm mt-1">
                            <p class="mb-2">By cancelling this deal, you acknowledge that:</p>
                            <ul class="list-disc list-inside space-y-1">
                                <li>You will NOT receive payment for this deal</li>
                                <li>The business will be notified of your cancellation</li>
                                <li>This action cannot be undone</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('athlete.deals.cancel.store', $deal) }}">
                    @csrf

                    <div class="form-control mb-6">
                        <label class="label" for="cancellation_reason">
                            <span class="label-text font-semibold">Reason for Cancellation <span class="text-error">*</span></span>
                            <span class="label-text-alt">Required (10-1000 characters)</span>
                        </label>
                        <textarea
                            id="cancellation_reason"
                            name="cancellation_reason"
                            class="textarea textarea-bordered h-32 @error('cancellation_reason') textarea-error @enderror"
                            placeholder="Please provide a detailed reason for cancelling this deal. This will be shared with the business."
                            required
                            minlength="10"
                            maxlength="1000"
                        >{{ old('cancellation_reason') }}</textarea>
                        <label class="label">
                            <span class="label-text-alt text-base-content/60">
                                This reason will be shared with the business and stored for record-keeping purposes.
                            </span>
                        </label>
                        @error('cancellation_reason')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <button type="submit" class="btn btn-error" onclick="return confirm('Are you sure you want to cancel this deal? You will receive payment, but this action cannot be undone.');">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Cancel Deal
                        </button>
                        <a href="{{ route('athlete.deals.show', $deal) }}" class="btn btn-ghost">
                            Go Back
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-athlete-dashboard-layout>

