<x-superadmin-dashboard-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Business Management</h2>
    </x-slot>

    <div x-data="{
        selectedBusinesses: [],
        showDeleteModal: false,
        showBulkActions: false,
        showSingleDeleteModal: false,
        showImpersonateModal: false,
        deleteBusinessId: null,
        deleteBusinessName: '',
        actionBusinessId: null,
        actionBusinessName: '',
        filters: {
            search: '{{ request('search', '') }}',
            status: '{{ request('status', '') }}'
        },
        toggleAll() {
            const allIds = {{ json_encode($businesses->pluck('id')->toArray()) }};
            if (this.selectedBusinesses.length === allIds.length && allIds.length > 0) {
                this.selectedBusinesses = [];
            } else {
                this.selectedBusinesses = [...allIds];
            }
            this.updateBulkActions();
        },
        toggleBusiness(id) {
            const index = this.selectedBusinesses.indexOf(id);
            if (index > -1) {
                this.selectedBusinesses.splice(index, 1);
            } else {
                this.selectedBusinesses.push(id);
            }
            this.updateBulkActions();
        },
        updateBulkActions() {
            this.showBulkActions = this.selectedBusinesses.length > 0;
        },
        confirmDelete() {
            if (this.selectedBusinesses.length > 0) {
                this.showDeleteModal = true;
            }
        },
        performDelete() {
            const form = document.getElementById('bulk-delete-form');
            form.submit();
        },
        applyFilters() {
            const params = new URLSearchParams();
            if (this.filters.search) params.append('search', this.filters.search);
            if (this.filters.status) params.append('status', this.filters.status);
            window.location.href = '{{ route('admin.businesses.index') }}?' + params.toString();
        },
        clearFilters() {
            this.filters = { search: '', status: '' };
            window.location.href = '{{ route('admin.businesses.index') }}';
        }
    }" class="space-y-6">
        <!-- Filters -->
        <div class="card bg-base-100 shadow-sm border border-base-300">
            <div class="card-body p-4">
                <form method="GET" action="{{ route('admin.businesses.index') }}" class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Search by name, email, or business name..." 
                               class="input input-bordered w-full">
                    </div>
                    <select name="status" class="select select-bordered">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                    <button type="submit" class="btn btn-primary">Search</button>
                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('admin.businesses.index') }}" class="btn btn-ghost">Clear</a>
                    @endif
                </form>
            </div>
        </div>

        <!-- Bulk Actions Bar -->
        <div x-show="showBulkActions" 
             x-transition
             class="alert bg-base-200 border border-base-300"
             style="display: none;">
            <div class="flex items-center justify-between w-full">
                <span class="text-sm font-medium">
                    <span x-text="selectedBusinesses.length"></span> business(es) selected
                </span>
                <div class="flex items-center gap-2">
                    <button @click="confirmDelete()" class="btn btn-error btn-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete Selected
                    </button>
                    <button @click="selectedBusinesses = []; showBulkActions = false;" class="btn btn-ghost btn-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>

        <!-- Businesses Table -->
        <div class="card bg-base-100 shadow-sm border border-base-300 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="w-12">
                                <input 
                                    type="checkbox" 
                                    @click="toggleAll()"
                                    :checked="selectedBusinesses.length === {{ $businesses->count() }} && selectedBusinesses.length > 0"
                                    class="checkbox checkbox-primary"
                                >
                            </th>
                            <th>Business</th>
                            <th>Business Name</th>
                            <th>Deals</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($businesses as $business)
                            <tr :class="{ 'bg-base-200': selectedBusinesses.includes({{ $business->id }}) }">
                                <td>
                                    <input 
                                        type="checkbox" 
                                        :value="{{ $business->id }}"
                                        @click="toggleBusiness({{ $business->id }})"
                                        :checked="selectedBusinesses.includes({{ $business->id }})"
                                        class="checkbox checkbox-primary"
                                    >
                                </td>
                                <td>
                                    <div class="flex items-center">
                                        <div class="avatar placeholder">
                                            <div class="bg-neutral text-neutral-content rounded-full w-10">
                                                <span class="text-xs">{{ substr($business->name, 0, 1) }}</span>
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium">{{ $business->name }}</div>
                                            <div class="text-xs text-base-content/60">{{ $business->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-sm">
                                    {{ $business->business_name ?? 'N/A' }}
                                </td>
                                <td class="text-sm">
                                    {{ $business->deals_count }}
                                </td>
                                <td>
                                    @if($business->email_verified_at)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-error">Suspended</span>
                                    @endif
                                </td>
                                <td class="text-sm text-base-content/60">
                                    {{ $business->created_at->format('M d, Y') }}
                                </td>
                                <td>
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.businesses.show', $business) }}" class="btn btn-ghost btn-xs btn-square" title="View">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.businesses.edit', $business) }}" class="btn btn-ghost btn-xs btn-square" title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <button 
                                            @click="actionBusinessId = {{ $business->id }}; actionBusinessName = '{{ $business->name }}'; showImpersonateModal = true;" 
                                            class="btn btn-ghost btn-xs btn-square" 
                                            title="Impersonate">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                            </svg>
                                        </button>
                                        <button 
                                            @click="deleteBusinessId = {{ $business->id }}; deleteBusinessName = '{{ $business->name }}'; showSingleDeleteModal = true;" 
                                            class="btn btn-ghost btn-xs btn-square text-error" 
                                            title="Delete">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-8">
                                    <p class="text-base-content/60">No businesses found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($businesses->hasPages())
                <div class="card-body border-t border-base-300">
                    {{ $businesses->links() }}
                </div>
            @endif
        </div>

        <!-- Bulk Delete Confirmation Modal -->
        <dialog x-show="showDeleteModal" 
                @click.away="showDeleteModal = false"
                class="modal"
                :class="{ 'modal-open': showDeleteModal }">
            <div class="modal-box">
                <h3 class="font-bold text-lg">Delete Businesses</h3>
                <p class="py-4">
                    Are you sure you want to delete <span x-text="selectedBusinesses.length"></span> business(es)? This action cannot be undone.
                </p>
                <div class="modal-action">
                    <form id="bulk-delete-form" method="POST" action="{{ route('admin.users.bulk-delete') }}">
                        @csrf
                        @method('DELETE')
                        <template x-for="id in selectedBusinesses" :key="id">
                            <input type="hidden" name="user_ids[]" :value="id">
                        </template>
                        <button type="button" @click="performDelete()" class="btn btn-error">
                            <span x-text="'Delete ' + (selectedBusinesses.length > 1 ? 'Businesses' : 'Business')"></span>
                        </button>
                    </form>
                    <button @click="showDeleteModal = false" class="btn btn-ghost">Cancel</button>
                </div>
            </div>
            <form method="dialog" class="modal-backdrop">
                <button>close</button>
            </form>
        </dialog>

        <!-- Single Delete Confirmation Modal -->
        <dialog x-show="showSingleDeleteModal" 
                @click.away="showSingleDeleteModal = false"
                class="modal"
                :class="{ 'modal-open': showSingleDeleteModal }">
            <div class="modal-box">
                <h3 class="font-bold text-lg">Delete Business</h3>
                <p class="py-4">
                    Are you sure you want to delete <span x-text="deleteBusinessName"></span>? This action cannot be undone.
                </p>
                <div class="modal-action">
                    <form method="POST" :action="'{{ url('/admin/businesses') }}/' + deleteBusinessId" id="single-delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-error">Delete Business</button>
                    </form>
                    <button @click="showSingleDeleteModal = false" class="btn btn-ghost">Cancel</button>
                </div>
            </div>
            <form method="dialog" class="modal-backdrop">
                <button>close</button>
            </form>
        </dialog>

        <!-- Impersonate Business Modal -->
        <dialog x-show="showImpersonateModal" 
                @click.away="showImpersonateModal = false"
                class="modal"
                :class="{ 'modal-open': showImpersonateModal }">
            <div class="modal-box">
                <h3 class="font-bold text-lg">Impersonate Business</h3>
                <p class="py-4">
                    Are you sure you want to impersonate <span x-text="actionBusinessName"></span>? You will be logged in as this business. Use the admin panel to stop impersonating.
                </p>
                <div class="modal-action">
                    <form method="POST" :action="'{{ url('/admin/users') }}/' + actionBusinessId + '/impersonate'">
                        @csrf
                        <button type="submit" class="btn btn-info">Impersonate</button>
                    </form>
                    <button @click="showImpersonateModal = false" class="btn btn-ghost">Cancel</button>
                </div>
            </div>
            <form method="dialog" class="modal-backdrop">
                <button>close</button>
            </form>
        </dialog>
    </div>
</x-superadmin-dashboard-layout>

