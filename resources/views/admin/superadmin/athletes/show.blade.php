<x-superadmin-dashboard-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">Athlete Profile: {{ $athlete->name }}</h2>
    </x-slot>

    <div x-data="{
        showHideModal: false,
    }" class="space-y-6">
        <div class="card bg-base-100 shadow-sm border border-base-300">
            <div class="card-body">
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <dt class="text-sm font-medium text-base-content/60">Name</dt>
                    <dd class="mt-1 text-sm font-medium">{{ $athlete->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-base-content/60">Email</dt>
                    <dd class="mt-1 text-sm">{{ $athlete->email }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-base-content/60">Level</dt>
                    <dd class="mt-1 text-sm">{{ ucfirst($athlete->athlete_level ?? 'N/A') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-base-content/60">Sport</dt>
                    <dd class="mt-1 text-sm">{{ $athlete->sport ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-base-content/60">School/Team</dt>
                    <dd class="mt-1 text-sm">{{ $athlete->school ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-base-content/60">Status</dt>
                    <dd class="mt-1">
                        @if($athlete->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-error">Hidden</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-base-content/60">Profile URL</dt>
                    <dd class="mt-1 text-sm">
                        <a href="{{ $athlete->profile_url }}" target="_blank" class="link link-primary">
                            {{ $athlete->profile_url }}
                        </a>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-base-content/60">Total Deals</dt>
                    <dd class="mt-1 text-sm font-medium">{{ $athlete->deals_count }}</dd>
                </div>
            </dl>

            <div class="mt-6 pt-6 border-t border-base-300 flex flex-wrap gap-2">
                <a href="{{ route('admin.athletes.index') }}" class="btn btn-ghost">
                    Back to Athletes
                </a>
                @if($athlete->is_active)
                    <button @click="showHideModal = true" class="btn btn-error">
                        Hide Profile
                    </button>
                @else
                    <form method="POST" action="{{ route('admin.athletes.show-profile', $athlete) }}" class="inline">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            Show Profile
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Deals -->
        <div class="card bg-base-100 shadow-sm border border-base-300">
            <div class="card-body">
                <h3 class="card-title mb-4">Deals</h3>
                @if($deals->isEmpty())
                    <p class="text-sm text-base-content/60">No deals found for this athlete.</p>
                @else
                    <div class="space-y-4">
                        @foreach($deals as $deal)
                            <div class="flex items-center justify-between border-b border-base-300 pb-3 last:border-0 last:pb-0">
                                <div>
                                    <p class="text-sm font-medium">Deal #{{ $deal->id }} - {{ $deal->deal_type }}</p>
                                    <p class="text-xs text-base-content/60">${{ number_format($deal->compensation_amount, 2) }} - {{ $deal->user->name ?? 'Unknown' }}</p>
                                </div>
                                <span class="badge badge-ghost">
                                    {{ ucfirst($deal->status) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        {{ $deals->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Hide Athlete Modal -->
        <dialog x-show="showHideModal" 
                @click.away="showHideModal = false"
                class="modal"
                :class="{ 'modal-open': showHideModal }">
            <div class="modal-box">
                <h3 class="font-bold text-lg">Hide Athlete Profile</h3>
                <p class="py-4">
                    Are you sure you want to hide {{ $athlete->name }}'s profile? This will make it invisible to the public.
                </p>
                <div class="modal-action">
                    <form method="POST" action="{{ route('admin.athletes.hide', $athlete) }}">
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
    </div>
</x-superadmin-dashboard-layout>

