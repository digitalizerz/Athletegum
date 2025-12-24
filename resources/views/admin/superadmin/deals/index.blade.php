<x-superadmin-dashboard-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold">Deal Oversight</h1>
    </x-slot>

    <div x-data="{
        selectedDeals: [],
        showDeleteModal: false,
        showBulkActions: false,
        showCancelModal: false,
        actionDealId: null,
        filters: {
            status: '{{ request('status', '') }}',
            search: '{{ request('search', '') }}'
        },
        toggleAll() {
            const allIds = {{ json_encode($deals->pluck('id')->toArray()) }};
            if (this.selectedDeals.length === allIds.length && allIds.length > 0) {
                this.selectedDeals = [];
            } else {
                this.selectedDeals = [...allIds];
            }
            this.updateBulkActions();
        },
        toggleDeal(id) {
            const index = this.selectedDeals.indexOf(id);
            if (index > -1) {
                this.selectedDeals.splice(index, 1);
            } else {
                this.selectedDeals.push(id);
            }
            this.updateBulkActions();
        },
        updateBulkActions() {
            this.showBulkActions = this.selectedDeals.length > 0;
        },
        confirmDelete() {
            if (this.selectedDeals.length > 0) {
                this.showDeleteModal = true;
            }
        },
        performDelete() {
            const form = document.getElementById('bulk-delete-form');
            form.submit();
        },
        applyFilters() {
            const params = new URLSearchParams();
            if (this.filters.status) params.append('status', this.filters.status);
            if (this.filters.search) params.append('search', this.filters.search);
            window.location.href = '{{ route('admin.deals.index') }}?' + params.toString();
        },
        clearFilters() {
            this.filters = { status: '', search: '' };
            window.location.href = '{{ route('admin.deals.index') }}';
        }
    }" class="space-y-6">
        <!-- Filters -->
        <div class="card bg-base-100 shadow-sm border border-base-300">
            <div class="card-body p-4">
                <form method="GET" action="{{ route('admin.deals.index') }}" class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Search deals..." 
                               class="input input-bordered w-full">
                    </div>
                    <select name="status" class="select select-bordered">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    <button type="submit" class="btn btn-primary">Filter</button>
                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('admin.deals.index') }}" class="btn btn-ghost">Clear</a>
                    @endif
                </form>
            </div>
        </div>

        <!-- Bulk Actions Bar -->
        <div x-show="showBulkActions" 
             x-transition
             class="bg-base-200 border border-base-300 rounded-lg px-4 py-3"
             style="display: none;">
            <div class="flex items-center justify-between w-full">
                <span class="text-sm text-base-content/70">
                    <span x-text="selectedDeals.length"></span> deal(s) selected
                </span>
                <div class="flex items-center gap-2">
                    <button @click="selectedDeals = []; showBulkActions = false;" class="btn btn-ghost btn-sm">
                        Cancel
                    </button>
                    <button @click="confirmDelete()" class="btn btn-outline btn-error btn-sm">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete
                    </button>
                </div>
            </div>
        </div>

        <!-- Deals Table -->
        <div class="card bg-base-100 shadow-sm border border-base-300 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="w-12">
                                <input 
                                    type="checkbox" 
                                    @click="toggleAll()"
                                    :checked="selectedDeals.length === {{ $deals->count() }} && selectedDeals.length > 0"
                                    class="checkbox checkbox-primary"
                                >
                            </th>
                            <th>Deal</th>
                            <th>Business</th>
                            <th>Athlete</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Escrow Status</th>
                            <th>Created</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($deals as $deal)
                            <tr :class="{ 'bg-base-200': selectedDeals.includes({{ $deal->id }}) }">
                                <td>
                                    <input 
                                        type="checkbox" 
                                        :value="{{ $deal->id }}"
                                        @click="toggleDeal({{ $deal->id }})"
                                        :checked="selectedDeals.includes({{ $deal->id }})"
                                        class="checkbox checkbox-primary"
                                    >
                                </td>
                                <td>
                                    <div>
                                        <div class="font-medium">#{{ $deal->id }}</div>
                                        <div class="text-sm text-base-content/60">{{ $deal->deal_type }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-sm">{{ $deal->user->name ?? 'N/A' }}</div>
                                    <div class="text-xs text-base-content/60">{{ $deal->user->email ?? '' }}</div>
                                </td>
                                <td class="text-sm">
                                    @if($deal->athlete)
                                        {{ $deal->athlete->name }}
                                    @else
                                        <span class="text-base-content/50 italic">Unassigned</span>
                                    @endif
                                </td>
                                <td class="text-sm">
                                    <div class="font-medium">
                                        ${{ number_format($deal->compensation_amount, 2) }}
                                    </div>
                                    @if($deal->isInEscrow())
                                        <div class="text-xs text-base-content/50 mt-0.5">
                                            ${{ number_format($deal->escrow_amount, 2) }} in escrow
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusBadges = [
                                            'pending' => 'badge-ghost',
                                            'sent' => 'badge-ghost',
                                            'active' => 'badge-ghost',
                                            'completed' => 'badge-ghost',
                                            'cancelled' => 'badge-error',
                                        ];
                                        $statusBadge = $statusBadges[$deal->status] ?? 'badge-ghost';
                                    @endphp
                                    <span class="badge {{ $statusBadge }} badge-sm">
                                        {{ ucfirst($deal->status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $deal->getEscrowStatusBadge() }} badge-sm">
                                        {{ $deal->getEscrowStatus() }}
                                    </span>
                                </td>
                                <td class="text-sm text-base-content/60">
                                    {{ $deal->created_at->format('M d, Y') }}
                                </td>
                                <td>
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.deals.show', $deal) }}" class="btn btn-ghost btn-xs" title="View Details">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <span class="sr-only">View</span>
                                        </a>
                                        @if(!in_array($deal->status, ['completed', 'cancelled']))
                                            <button 
                                                @click="actionDealId = {{ $deal->id }}; showCancelModal = true;" 
                                                class="btn btn-ghost btn-xs text-error" 
                                                title="Cancel Deal">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                                <span class="sr-only">Cancel</span>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-8">
                                    <p class="text-base-content/60">No deals found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($deals->hasPages())
                <div class="card-body border-t border-base-300">
                    {{ $deals->links() }}
                </div>
            @endif
        </div>

        <!-- Delete Confirmation Modal -->
        <dialog x-show="showDeleteModal" 
                @click.away="showDeleteModal = false"
                class="modal"
                :class="{ 'modal-open': showDeleteModal }">
            <div class="modal-box">
                <h3 class="font-bold text-lg">Delete Deals</h3>
                <p class="py-4">
                    Are you sure you want to delete <span x-text="selectedDeals.length"></span> deal(s)? This action cannot be undone.
                </p>
                <div class="modal-action">
                    <form id="bulk-delete-form" method="POST" action="{{ route('admin.deals.bulk-delete') }}">
                        @csrf
                        @method('DELETE')
                        <template x-for="id in selectedDeals" :key="id">
                            <input type="hidden" name="deal_ids[]" :value="id">
                        </template>
                        <button type="button" @click="performDelete()" class="btn btn-error">
                            <span x-text="'Delete ' + (selectedDeals.length > 1 ? 'Deals' : 'Deal')"></span>
                        </button>
                    </form>
                    <button @click="showDeleteModal = false" class="btn btn-ghost">Cancel</button>
                </div>
            </div>
            <form method="dialog" class="modal-backdrop">
                <button>close</button>
            </form>
        </dialog>

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
                    <form method="POST" :action="'{{ url('/admin/deals') }}/' + actionDealId + '/cancel'">
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
