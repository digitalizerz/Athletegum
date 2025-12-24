<x-superadmin-dashboard-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Athlete Profile Management</h2>
    </x-slot>

    <div x-data="{
        selectedAthletes: [],
        showDeleteModal: false,
        showBulkActions: false,
        showHideModal: false,
        showSingleDeleteModal: false,
        deleteAthleteId: null,
        deleteAthleteName: '',
        actionAthleteId: null,
        actionAthleteName: '',
        filters: {
            search: '{{ request('search', '') }}',
            is_active: '{{ request('is_active', '') }}'
        },
        toggleAll() {
            const allIds = {{ json_encode($athletes->pluck('id')->toArray()) }};
            if (this.selectedAthletes.length === allIds.length && allIds.length > 0) {
                this.selectedAthletes = [];
            } else {
                this.selectedAthletes = [...allIds];
            }
            this.updateBulkActions();
        },
        toggleAthlete(id) {
            const index = this.selectedAthletes.indexOf(id);
            if (index > -1) {
                this.selectedAthletes.splice(index, 1);
            } else {
                this.selectedAthletes.push(id);
            }
            this.updateBulkActions();
        },
        updateBulkActions() {
            this.showBulkActions = this.selectedAthletes.length > 0;
        },
        confirmDelete() {
            if (this.selectedAthletes.length > 0) {
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
            if (this.filters.is_active) params.append('is_active', this.filters.is_active);
            window.location.href = '{{ route('admin.athletes.index') }}?' + params.toString();
        },
        clearFilters() {
            this.filters = { search: '', is_active: '' };
            window.location.href = '{{ route('admin.athletes.index') }}';
        }
    }" class="space-y-6">
        <!-- Filters -->
        <div class="card bg-base-100 shadow-sm border border-base-300">
            <div class="card-body p-4">
                <form method="GET" action="{{ route('admin.athletes.index') }}" class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Search by name or email..." 
                               class="input input-bordered w-full">
                    </div>
                    <select name="is_active" class="select select-bordered">
                        <option value="">All Statuses</option>
                        <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Hidden</option>
                    </select>
                    <button type="submit" class="btn btn-primary">Search</button>
                    @if(request()->hasAny(['search', 'is_active']))
                        <a href="{{ route('admin.athletes.index') }}" class="btn btn-ghost">Clear</a>
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
                    <span x-text="selectedAthletes.length"></span> athlete(s) selected
                </span>
                <div class="flex items-center gap-2">
                    <button @click="confirmDelete()" class="btn btn-error btn-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete Selected
                    </button>
                    <button @click="selectedAthletes = []; showBulkActions = false;" class="btn btn-ghost btn-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>

        <!-- Athletes Table -->
        <div class="card bg-base-100 shadow-sm border border-base-300 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="w-12">
                                <input 
                                    type="checkbox" 
                                    @click="toggleAll()"
                                    :checked="selectedAthletes.length === {{ $athletes->count() }} && selectedAthletes.length > 0"
                                    class="checkbox checkbox-primary"
                                >
                            </th>
                            <th>Athlete</th>
                            <th>Level</th>
                            <th>Deals</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($athletes as $athlete)
                            <tr :class="{ 'bg-base-200': selectedAthletes.includes({{ $athlete->id }}) }">
                                <td>
                                    <input 
                                        type="checkbox" 
                                        :value="{{ $athlete->id }}"
                                        @click="toggleAthlete({{ $athlete->id }})"
                                        :checked="selectedAthletes.includes({{ $athlete->id }})"
                                        class="checkbox checkbox-primary"
                                    >
                                </td>
                                <td>
                                    <div class="flex items-center">
                                        @if($athlete->profile_photo)
                                            <img src="{{ asset('storage/' . $athlete->profile_photo) }}" alt="" class="h-10 w-10 rounded-full mr-3">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-base-300 mr-3 flex items-center justify-center">
                                                <svg class="h-6 w-6 text-base-content/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium">{{ $athlete->name }}</div>
                                            <div class="text-xs text-base-content/60">{{ $athlete->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-sm">
                                    {{ ucfirst($athlete->athlete_level ?? 'N/A') }}
                                </td>
                                <td class="text-sm">
                                    {{ $athlete->deals_count }}
                                </td>
                                <td>
                                    @if($athlete->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-error">Hidden</span>
                                    @endif
                                </td>
                                <td class="text-sm text-base-content/60">
                                    {{ $athlete->created_at->format('M d, Y') }}
                                </td>
                                <td>
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.athletes.show', $athlete) }}" class="btn btn-ghost btn-xs btn-square" title="View">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.athletes.edit', $athlete) }}" class="btn btn-ghost btn-xs btn-square" title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        @if($athlete->is_active)
                                            <button 
                                                @click="actionAthleteId = {{ $athlete->id }}; actionAthleteName = '{{ $athlete->name }}'; showHideModal = true;" 
                                                class="btn btn-ghost btn-xs btn-square text-error" 
                                                title="Hide">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0L3 10.17M6.59 6.59L3 3m3.59 3.59l3.59 3.59" />
                                                </svg>
                                            </button>
                                        @else
                                            <form method="POST" action="{{ route('admin.athletes.show-profile', $athlete) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="btn btn-ghost btn-xs btn-square text-success" title="Show">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                        <button 
                                            @click="deleteAthleteId = {{ $athlete->id }}; deleteAthleteName = '{{ $athlete->name }}'; showSingleDeleteModal = true;" 
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
                                    <p class="text-base-content/60">No athletes found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($athletes->hasPages())
                <div class="card-body border-t border-base-300">
                    {{ $athletes->links() }}
                </div>
            @endif
        </div>

        <!-- Delete Confirmation Modal -->
        <dialog x-show="showDeleteModal" 
                @click.away="showDeleteModal = false"
                class="modal"
                :class="{ 'modal-open': showDeleteModal }">
            <div class="modal-box">
                <h3 class="font-bold text-lg">Delete Athletes</h3>
                <p class="py-4">
                    Are you sure you want to delete <span x-text="selectedAthletes.length"></span> athlete(s)? This action cannot be undone.
                </p>
                <div class="modal-action">
                    <form id="bulk-delete-form" method="POST" action="{{ route('admin.athletes.bulk-delete') }}">
                        @csrf
                        @method('DELETE')
                        <template x-for="id in selectedAthletes" :key="id">
                            <input type="hidden" name="athlete_ids[]" :value="id">
                        </template>
                        <button type="button" @click="performDelete()" class="btn btn-error">
                            <span x-text="'Delete ' + (selectedAthletes.length > 1 ? 'Athletes' : 'Athlete')"></span>
                        </button>
                    </form>
                    <button @click="showDeleteModal = false" class="btn btn-ghost">Cancel</button>
                </div>
            </div>
            <form method="dialog" class="modal-backdrop">
                <button>close</button>
            </form>
        </dialog>

        <!-- Hide Athlete Modal -->
        <dialog x-show="showHideModal" 
                @click.away="showHideModal = false"
                class="modal"
                :class="{ 'modal-open': showHideModal }">
            <div class="modal-box">
                <h3 class="font-bold text-lg">Hide Athlete Profile</h3>
                <p class="py-4">
                    Are you sure you want to hide <span x-text="actionAthleteName"></span>'s profile? This will make it invisible to the public.
                </p>
                <div class="modal-action">
                    <form method="POST" :action="'{{ url('/admin/athletes') }}/' + actionAthleteId + '/hide'">
                        @csrf
                        <button type="submit" class="btn btn-error">Hide Profile</button>
                    </form>
                    <button @click="showHideModal = false" class="btn btn-ghost">Cancel</button>
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
                <h3 class="font-bold text-lg">Delete Athlete</h3>
                <p class="py-4">
                    Are you sure you want to delete <span x-text="deleteAthleteName"></span>? This action cannot be undone.
                </p>
                <div class="modal-action">
                    <form method="POST" :action="'{{ url('/admin/athletes') }}/' + deleteAthleteId" id="single-delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-error">Delete Athlete</button>
                    </form>
                    <button @click="showSingleDeleteModal = false" class="btn btn-ghost">Cancel</button>
                </div>
            </div>
            <form method="dialog" class="modal-backdrop">
                <button>close</button>
            </form>
        </dialog>
    </div>
</x-superadmin-dashboard-layout>
