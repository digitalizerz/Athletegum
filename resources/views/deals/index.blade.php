<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-semibold">Deals</h2>
            <a href="{{ route('deals.create') }}">
                <button class="btn btn-primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Create New Deal
                </button>
            </a>
        </div>
    </x-slot>

    <!-- Success Message -->
    @if(session('success'))
        <div role="alert" class="alert alert-success mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Error Message -->
    @if($errors->any())
        <div role="alert" class="alert alert-error mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <div x-data="{
        selectedDeals: [],
        showDeleteModal: false,
        showBulkActions: false,
        showReleaseModal: false,
        releaseDealId: null,
        releaseAmount: null,
        showRequestRevisionModal: false,
        requestRevisionDealId: null,
        filters: {
            status: '{{ request('status', '') }}',
            deal_type: '{{ request('deal_type', '') }}',
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
            if (this.filters.deal_type) params.append('deal_type', this.filters.deal_type);
            if (this.filters.search) params.append('search', this.filters.search);
            window.location.href = '{{ route('deals.index') }}?' + params.toString();
        },
        clearFilters() {
            this.filters = { status: '', deal_type: '', search: '' };
            window.location.href = '{{ route('deals.index') }}';
        }
    }" class="space-y-6">
        <!-- Compact Filters -->
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body p-3">
                <div class="flex flex-wrap items-center gap-3">
                    <input 
                        type="text" 
                        id="search" 
                        x-model="filters.search"
                        @keyup.enter="applyFilters()"
                        placeholder="Search..."
                        class="input input-bordered flex-1 min-w-[200px] input-sm"
                    >
                    <select 
                        id="status" 
                        x-model="filters.status"
                        @change="applyFilters()"
                        class="select select-bordered select-sm"
                    >
                        <option value="">All Statuses</option>
                        <option value="draft">Draft</option>
                        <option value="pending">Pending</option>
                        <option value="accepted">Accepted</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                    <select 
                        id="deal_type" 
                        x-model="filters.deal_type"
                        @change="applyFilters()"
                        class="select select-bordered select-sm"
                    >
                        <option value="">All Types</option>
                        @foreach(\App\Models\Deal::getDealTypes() as $key => $type)
                            <option value="{{ $key }}">{{ $type['name'] }}</option>
                        @endforeach
                    </select>
                    <button @click="clearFilters()" class="btn btn-ghost btn-sm">
                        Clear
                    </button>
                </div>
            </div>
        </div>

        <!-- Bulk Actions Bar -->
        <div x-show="showBulkActions" 
             x-transition
             class="alert bg-base-200 border border-base-300"
             style="display: none;">
            <div class="flex items-center justify-between w-full">
                <span class="text-sm font-medium">
                    <span x-text="selectedDeals.length"></span> deal(s) selected
                </span>
                <div class="flex items-center gap-2">
                    <button @click="confirmDelete()" class="btn btn-error btn-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete Selected
                    </button>
                    <button @click="selectedDeals = []; showBulkActions = false;" class="btn btn-ghost btn-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>

        @if($deals->isEmpty())
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-base-content/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium">No deals found</h3>
                    <p class="mt-2 text-sm text-base-content/60">Try adjusting your filters or create your first deal.</p>
                    <div class="mt-6">
                        <a href="{{ route('deals.create') }}">
                            <button class="btn btn-primary">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Create Your First Deal
                            </button>
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="card bg-base-100 shadow-sm overflow-hidden">
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
                                <th>Amount</th>
                                <th>Deadline</th>
                                <th>Status</th>
                                <th class="hidden sm:table-cell">Created</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($deals as $deal)
                                @php
                                    $dealTypes = \App\Models\Deal::getDealTypes();
                                    $dealTypeInfo = $dealTypes[$deal->deal_type] ?? null;
                                    $platforms = \App\Models\Deal::getPlatforms();
                                @endphp
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
                                        <div class="flex items-center">
                                            @if($dealTypeInfo)
                                                <span class="text-2xl mr-3">{{ $dealTypeInfo['icon'] ?? '📋' }}</span>
                                            @endif
                                            <div>
                                                <div class="font-medium">
                                                    {{ $dealTypeInfo['name'] ?? $deal->deal_type }}
                                                </div>
                                                @if(!empty($deal->platforms) && is_array($deal->platforms))
                                                    <div class="text-xs text-base-content/60 mt-1">
                                                        @foreach($deal->platforms as $platform)
                                                            @if(isset($platforms[$platform]))
                                                                <span class="badge badge-sm badge-outline mr-1">
                                                                    {{ $platforms[$platform] }}
                                                                </span>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="font-semibold">
                                            ${{ number_format($deal->compensation_amount, 2) }}
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            {{ \Carbon\Carbon::parse($deal->deadline)->format('M j, Y') }}
                                        </div>
                                        @php
                                            $daysDiff = \Carbon\Carbon::parse($deal->deadline)->diffInDays(now(), false);
                                        @endphp
                                        @if($daysDiff > 0)
                                            <div class="text-xs text-error font-medium">Past due</div>
                                        @elseif($daysDiff == 0)
                                            <div class="text-xs text-warning font-medium">Due today</div>
                                        @elseif(abs($daysDiff) <= 7)
                                            <div class="text-xs text-warning font-medium">
                                                Due in {{ abs($daysDiff) }} {{ abs($daysDiff) === 1 ? 'day' : 'days' }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statusBadges = [
                                                'draft' => 'badge-ghost',
                                                'pending' => 'badge-warning',
                                                'accepted' => 'badge-info',
                                                'active' => 'badge-info',
                                                'completed' => 'badge-success',
                                                'cancelled' => 'badge-error',
                                            ];
                                            $statusBadge = $statusBadges[$deal->status] ?? 'badge-ghost';
                                        @endphp
                                        <span class="badge {{ $statusBadge }}">
                                            {{ ucfirst($deal->status) }}
                                        </span>
                                        @if($deal->isInEscrow())
                                            <div class="text-xs text-base-content/60 mt-1">
                                                @if($deal->awaiting_funds)
                                                    <span class="badge badge-warning badge-sm" title="Payment complete – payout pending clearing">Payout Pending Clearing</span>
                                                @else
                                                    <span class="badge badge-warning badge-sm">In Escrow</span>
                                                @endif
                                            </div>
                                        @elseif($deal->released_at)
                                            <div class="text-xs text-base-content/60 mt-1">
                                                <span class="badge badge-success badge-sm">Released</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="hidden sm:table-cell text-base-content/60">
                                        {{ \Carbon\Carbon::parse($deal->created_at)->format('M j, Y') }}
                                    </td>
                                    <td>
                                        <div class="flex items-center justify-end gap-2">
                                            @if($deal->status === 'draft')
                                                <a href="{{ route('deals.resume-draft', $deal) }}" class="btn btn-primary btn-xs" title="Resume Draft">
                                                    Resume
                                                </a>
                                            @else
                                                <a href="{{ route('deals.success', $deal) }}" class="btn btn-ghost btn-xs btn-square" title="View">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </a>
                                            @endif
                                            @if($deal->athlete_id && $deal->status !== 'pending' && $deal->status !== 'draft')
                                                <a href="{{ route('deals.messages', $deal) }}" class="btn btn-ghost btn-xs btn-square" title="Messages">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                                    </svg>
                                                </a>
                                            @endif
                                            @if(!in_array($deal->status, ['completed', 'cancelled', 'draft']) && !$deal->released_at)
                                                <a href="{{ route('deals.edit', $deal) }}" class="btn btn-ghost btn-xs btn-square" title="Edit">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                            @endif
                                            @if(($deal->status === 'completed' || $deal->status === 'approved') && !$deal->released_at)
                                                <button 
                                                    @click="requestRevisionDealId = {{ $deal->id }}; showRequestRevisionModal = true;" 
                                                    class="btn btn-ghost btn-xs btn-square text-warning" 
                                                    title="Request Revisions">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>
                                            @endif
                                            @if($deal->canBeReleased())
                                                <button 
                                                    @click="releaseDealId = {{ $deal->id }}; releaseAmount = {{ $deal->escrow_amount }}; showReleaseModal = true;" 
                                                    class="btn btn-ghost btn-xs btn-square text-success" 
                                                    title="Release Payment">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </button>
                                            @endif
                                            <button 
                                                @click="selectedDeals = [{{ $deal->id }}]; confirmDelete();"
                                                class="btn btn-ghost btn-xs btn-square text-error" 
                                                title="Delete"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if($deals->hasPages())
                    <div class="card-body border-t border-base-300">
                        {{ $deals->links() }}
                    </div>
                @endif
            </div>
        @endif

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
                    <form id="bulk-delete-form" method="POST" action="{{ route('deals.bulk-delete') }}">
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

        <!-- Request Revisions Modal -->
        <dialog x-show="showRequestRevisionModal" 
                @click.away="showRequestRevisionModal = false"
                class="modal"
                :class="{ 'modal-open': showRequestRevisionModal }">
            <div class="modal-box max-w-2xl">
                <form method="POST" :action="'{{ url('/deals') }}/' + requestRevisionDealId + '/request-revisions'" id="request-revision-form">
                    @csrf
                    
                    {{-- Header Section --}}
                    <div class="mb-6">
                        <h3 class="font-bold text-xl text-base-content mb-2">Request Revisions</h3>
                        <p class="text-sm text-base-content/70">
                            Send this deal back to the athlete with clear feedback on what needs to be updated.
                        </p>
                    </div>

                    {{-- Form Area --}}
                    <div class="form-control mb-6">
                        <label class="label pb-2">
                            <span class="label-text font-semibold text-base-content">
                                Revision Notes <span class="text-error">*</span>
                                <span class="font-normal text-base-content/60 text-xs ml-2">(Required)</span>
                            </span>
                        </label>
                        <p class="text-sm text-base-content/60 mb-3">
                            Be specific so the athlete can quickly make the requested changes.
                        </p>
                        <textarea
                            name="revision_notes"
                            class="textarea textarea-bordered w-full h-32 resize-none @error('revision_notes') textarea-error @enderror"
                            placeholder="Please adjust the caption to include our brand hashtag and resubmit the Instagram post link."
                            required
                            minlength="10"
                            maxlength="1000"
                        >{{ old('revision_notes') }}</textarea>
                        <label class="label pt-1">
                            <span class="label-text-alt text-base-content/50">
                                10-1000 characters
                            </span>
                        </label>
                        @error('revision_notes')
                            <label class="label pt-1">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    {{-- Footer Section --}}
                    <div class="pt-4 border-t border-base-300">
                        <p class="text-xs text-base-content/60 mb-4">
                            This message will be shared with the athlete and saved in the deal conversation.
                        </p>
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="showRequestRevisionModal = false" class="btn btn-ghost">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-primary">
                                Request Revisions
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <form method="dialog" class="modal-backdrop">
                <button @click="showRequestRevisionModal = false">close</button>
            </form>
        </dialog>

        <!-- Release Payment Modal -->
        <dialog x-show="showReleaseModal" 
                @click.away="showReleaseModal = false"
                class="modal"
                :class="{ 'modal-open': showReleaseModal }">
            <div class="modal-box">
                <h3 class="font-bold text-lg">Release Escrow Payment</h3>
                <p class="py-4">
                    Are you sure you want to release $<span x-text="releaseAmount ? releaseAmount.toFixed(2) : '0.00'"></span> from escrow to the athlete? 
                    <br><br>
                    <strong>This action:</strong>
                    <ul class="list-disc list-inside mt-2 text-sm">
                        <li>Releases funds from escrow to the athlete</li>
                        <li>Finalizes platform fees</li>
                        <li>Cannot be undone</li>
                    </ul>
                </p>
                <div class="modal-action">
                    <form method="POST" :action="'{{ url('/deals') }}/' + releaseDealId + '/release-payment'">
                        @csrf
                        <button type="submit" class="btn btn-success">Release Payment</button>
                    </form>
                    <button @click="showReleaseModal = false" class="btn btn-ghost">Cancel</button>
                </div>
            </div>
            <form method="dialog" class="modal-backdrop">
                <button>close</button>
            </form>
        </dialog>
    </div>
</x-app-layout>
