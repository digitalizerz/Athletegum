<x-superadmin-dashboard-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">Deal Details #{{ $deal->id }}</h2>
    </x-slot>

    <div x-data="{
        showCancelModal: false,
    }" class="space-y-6">
        <div class="card bg-base-100 shadow-sm border border-base-300">
            <div class="card-body">
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <dt class="text-sm font-medium text-base-content/60">Deal Type</dt>
                    <dd class="mt-1 text-sm font-medium">{{ $deal->deal_type }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-base-content/60">Status</dt>
                    <dd class="mt-1">
                        @php
                            $statusBadges = [
                                'pending' => 'badge-warning',
                                'sent' => 'badge-info',
                                'active' => 'badge-success',
                                'completed' => 'badge-ghost',
                                'cancelled' => 'badge-error',
                            ];
                            $badge = $statusBadges[$deal->status] ?? 'badge-ghost';
                        @endphp
                        <span class="badge {{ $badge }}">
                            {{ ucfirst($deal->status) }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-base-content/60">Business</dt>
                    <dd class="mt-1 text-sm font-medium">{{ $deal->user->name ?? 'N/A' }}</dd>
                    <dd class="text-sm text-base-content/60">{{ $deal->user->email ?? '' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-base-content/60">Athlete</dt>
                    <dd class="mt-1 text-sm font-medium">{{ $deal->athlete->name ?? 'Unassigned' }}</dd>
                    @if($deal->athlete)
                        <dd class="text-sm text-base-content/60">{{ $deal->athlete->email }}</dd>
                    @endif
                </div>
                <div>
                    <dt class="text-sm font-medium text-base-content/60">Compensation</dt>
                    <dd class="mt-1 text-sm font-medium">${{ number_format($deal->compensation_amount, 2) }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-base-content/60">Payment Status</dt>
                    <dd class="mt-1">
                        <span class="badge {{ $deal->payment_status === 'paid' ? 'badge-success' : 'badge-warning' }}">
                            {{ ucfirst($deal->payment_status ?? 'pending') }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-base-content/60">Deadline</dt>
                    <dd class="mt-1 text-sm">{{ $deal->deadline ? $deal->deadline->format('M d, Y') : 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-base-content/60">Created</dt>
                    <dd class="mt-1 text-sm">{{ $deal->created_at->format('M d, Y H:i') }}</dd>
                </div>
            </dl>

            @if($deal->notes)
                <div class="mt-6 pt-6 border-t border-base-300">
                    <dt class="text-sm font-medium text-base-content/60">Notes</dt>
                    <dd class="mt-1 text-sm">{{ $deal->notes }}</dd>
                </div>
            @endif

            <div class="mt-6 pt-6 border-t border-base-300 flex flex-wrap gap-2">
                <a href="{{ route('admin.deals.index') }}" class="btn btn-ghost">
                    Back to Deals
                </a>
                @if(!in_array($deal->status, ['completed', 'cancelled']))
                    <button @click="showCancelModal = true" class="btn btn-error">
                        Cancel Deal
                    </button>
                @endif
            </div>
        </div>

        <!-- Cancel Deal Modal -->
        <dialog x-show="showCancelModal" 
                @click.away="showCancelModal = false"
                class="modal"
                :class="{ 'modal-open': showCancelModal }">
            <div class="modal-box">
                <h3 class="font-bold text-lg">Cancel Deal</h3>
                <p class="py-4">
                    Are you sure you want to cancel this deal? This action cannot be undone.
                </p>
                <div class="modal-action">
                    <form method="POST" action="{{ route('admin.deals.cancel', $deal) }}">
                        @csrf
                        <button type="submit" class="btn btn-error">Cancel Deal</button>
                    </form>
                    <button @click="showCancelModal = false" class="btn btn-ghost">Close</button>
                </div>
            </div>
            <form method="dialog" class="modal-backdrop">
                <button>close</button>
            </form>
        </dialog>
    </div>
</x-superadmin-dashboard-layout>

