<x-superadmin-dashboard-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">User Management</h2>
    </x-slot>

    <div x-data="{
        selectedUsers: [],
        showDeleteModal: false,
        showBulkActions: false,
        showSuspendModal: false,
        showImpersonateModal: false,
        actionUserId: null,
        actionUserName: '',
        filters: {
            search: '{{ request('search', '') }}'
        },
        toggleAll() {
            const allIds = {{ json_encode($users->pluck('id')->toArray()) }};
            if (this.selectedUsers.length === allIds.length && allIds.length > 0) {
                this.selectedUsers = [];
            } else {
                this.selectedUsers = [...allIds];
            }
            this.updateBulkActions();
        },
        toggleUser(id) {
            const index = this.selectedUsers.indexOf(id);
            if (index > -1) {
                this.selectedUsers.splice(index, 1);
            } else {
                this.selectedUsers.push(id);
            }
            this.updateBulkActions();
        },
        updateBulkActions() {
            this.showBulkActions = this.selectedUsers.length > 0;
        },
        confirmDelete() {
            if (this.selectedUsers.length > 0) {
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
            window.location.href = '{{ route('admin.users.index') }}?' + params.toString();
        },
        clearFilters() {
            this.filters = { search: '' };
            window.location.href = '{{ route('admin.users.index') }}';
        }
    }" class="space-y-6">
        <!-- Filters -->
        <div class="card bg-base-100 shadow-sm border border-base-300">
            <div class="card-body p-4">
                <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Search by name or email..." 
                               class="input input-bordered w-full">
                    </div>
                    <button type="submit" class="btn btn-primary">Search</button>
                    @if(request()->hasAny(['search']))
                        <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Clear</a>
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
                    <span x-text="selectedUsers.length"></span> user(s) selected
                </span>
                <div class="flex items-center gap-2">
                    <button @click="confirmDelete()" class="btn btn-error btn-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete Selected
                    </button>
                    <button @click="selectedUsers = []; showBulkActions = false;" class="btn btn-ghost btn-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="card bg-base-100 shadow-sm border border-base-300 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="w-12">
                                <input 
                                    type="checkbox" 
                                    @click="toggleAll()"
                                    :checked="selectedUsers.length === {{ $users->count() }} && selectedUsers.length > 0"
                                    class="checkbox checkbox-primary"
                                >
                            </th>
                            <th>User</th>
                            <th>Deals</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr :class="{ 'bg-base-200': selectedUsers.includes({{ $user->id }}) }">
                                <td>
                                    <input 
                                        type="checkbox" 
                                        :value="{{ $user->id }}"
                                        @click="toggleUser({{ $user->id }})"
                                        :checked="selectedUsers.includes({{ $user->id }})"
                                        class="checkbox checkbox-primary"
                                    >
                                </td>
                                <td>
                                    <div>
                                        <div class="font-medium">{{ $user->name }}</div>
                                        <div class="text-sm text-base-content/60">{{ $user->email }}</div>
                                    </div>
                                </td>
                                <td class="text-sm">
                                    {{ $user->deals_count }}
                                </td>
                                <td>
                                    @if($user->email_verified_at)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-error">Suspended</span>
                                    @endif
                                </td>
                                <td class="text-sm text-base-content/60">
                                    {{ $user->created_at->format('M d, Y') }}
                                </td>
                                <td>
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-ghost btn-xs btn-square" title="View">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        @if($user->email_verified_at)
                                            <button 
                                                @click="actionUserId = {{ $user->id }}; actionUserName = '{{ $user->name }}'; showSuspendModal = true;" 
                                                class="btn btn-ghost btn-xs btn-square text-error" 
                                                title="Suspend">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                </svg>
                                            </button>
                                        @else
                                            <form method="POST" action="{{ route('admin.users.reactivate', $user) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="btn btn-ghost btn-xs btn-square text-success" title="Reactivate">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                        <button 
                                            @click="actionUserId = {{ $user->id }}; actionUserName = '{{ $user->name }}'; showImpersonateModal = true;" 
                                            class="btn btn-ghost btn-xs btn-square text-info" 
                                            title="Impersonate">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-8">
                                    <p class="text-base-content/60">No users found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($users->hasPages())
                <div class="card-body border-t border-base-300">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

        <!-- Delete Confirmation Modal -->
        <dialog x-show="showDeleteModal" 
                @click.away="showDeleteModal = false"
                class="modal"
                :class="{ 'modal-open': showDeleteModal }">
            <div class="modal-box">
                <h3 class="font-bold text-lg">Delete Users</h3>
                <p class="py-4">
                    Are you sure you want to delete <span x-text="selectedUsers.length"></span> user(s)? This action cannot be undone.
                </p>
                <div class="modal-action">
                    <form id="bulk-delete-form" method="POST" action="{{ route('admin.users.bulk-delete') }}">
                        @csrf
                        @method('DELETE')
                        <template x-for="id in selectedUsers" :key="id">
                            <input type="hidden" name="user_ids[]" :value="id">
                        </template>
                        <button type="button" @click="performDelete()" class="btn btn-error">
                            <span x-text="'Delete ' + (selectedUsers.length > 1 ? 'Users' : 'User')"></span>
                        </button>
                    </form>
                    <button @click="showDeleteModal = false" class="btn btn-ghost">Cancel</button>
                </div>
            </div>
            <form method="dialog" class="modal-backdrop">
                <button>close</button>
            </form>
        </dialog>

        <!-- Suspend User Modal -->
        <dialog x-show="showSuspendModal" 
                @click.away="showSuspendModal = false"
                class="modal"
                :class="{ 'modal-open': showSuspendModal }">
            <div class="modal-box">
                <h3 class="font-bold text-lg">Suspend User</h3>
                <p class="py-4">
                    Are you sure you want to suspend <span x-text="actionUserName"></span>? This will prevent them from accessing their account.
                </p>
                <div class="modal-action">
                    <form method="POST" :action="'{{ url('/admin/users') }}/' + actionUserId + '/suspend'">
                        @csrf
                        <button type="submit" class="btn btn-error">Suspend User</button>
                    </form>
                    <button @click="showSuspendModal = false" class="btn btn-ghost">Cancel</button>
                </div>
            </div>
            <form method="dialog" class="modal-backdrop">
                <button>close</button>
            </form>
        </dialog>

        <!-- Impersonate User Modal -->
        <dialog x-show="showImpersonateModal" 
                @click.away="showImpersonateModal = false"
                class="modal"
                :class="{ 'modal-open': showImpersonateModal }">
            <div class="modal-box">
                <h3 class="font-bold text-lg">Impersonate User</h3>
                <p class="py-4">
                    Are you sure you want to impersonate <span x-text="actionUserName"></span>? You will be logged in as this user. Use the admin panel to stop impersonating.
                </p>
                <div class="modal-action">
                    <form method="POST" :action="'{{ url('/admin/users') }}/' + actionUserId + '/impersonate'">
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
