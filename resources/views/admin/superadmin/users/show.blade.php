<x-superadmin-dashboard-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">User: {{ $user->name }}</h2>
    </x-slot>

    <div x-data="{
        showSuspendModal: false,
        showImpersonateModal: false,
    }" class="space-y-6">
        <div class="card bg-base-100 shadow-sm border border-base-300">
            <div class="card-body">
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <dt class="text-sm font-medium text-base-content/60">Name</dt>
                    <dd class="mt-1 text-sm font-medium">{{ $user->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-base-content/60">Email</dt>
                    <dd class="mt-1 text-sm">{{ $user->email }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-base-content/60">Status</dt>
                    <dd class="mt-1">
                        @if($user->email_verified_at)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-error">Suspended</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-base-content/60">Wallet Balance</dt>
                    <dd class="mt-1 text-sm font-medium">${{ number_format($user->wallet_balance ?? 0, 2) }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-base-content/60">Total Deals</dt>
                    <dd class="mt-1 text-sm font-medium">{{ $user->deals_count }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-base-content/60">Joined</dt>
                    <dd class="mt-1 text-sm">{{ $user->created_at->format('M d, Y') }}</dd>
                </div>
            </dl>

            <div class="mt-6 pt-6 border-t border-base-300 flex flex-wrap gap-2">
                <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">
                    Back to Users
                </a>
                @if($user->email_verified_at)
                    <button @click="showSuspendModal = true" class="btn btn-error">
                        Suspend User
                    </button>
                @else
                    <form method="POST" action="{{ route('admin.users.reactivate', $user) }}" class="inline">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            Reactivate User
                        </button>
                    </form>
                @endif
                <button @click="showImpersonateModal = true" class="btn btn-info">
                    Impersonate
                </button>
            </div>
        </div>

        <!-- Deals -->
        <div class="card bg-base-100 shadow-sm border border-base-300">
            <div class="card-body">
                <h3 class="card-title mb-4">Deals</h3>
                @if($deals->isEmpty())
                    <p class="text-sm text-base-content/60">No deals found for this user.</p>
                @else
                    <div class="space-y-4">
                        @foreach($deals as $deal)
                            <div class="flex items-center justify-between border-b border-base-300 pb-3 last:border-0 last:pb-0">
                                <div>
                                    <p class="text-sm font-medium">Deal #{{ $deal->id }} - {{ $deal->deal_type }}</p>
                                    <p class="text-xs text-base-content/60">${{ number_format($deal->compensation_amount, 2) }}</p>
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

        <!-- Suspend User Modal -->
        <dialog x-show="showSuspendModal" 
                @click.away="showSuspendModal = false"
                class="modal"
                :class="{ 'modal-open': showSuspendModal }">
            <div class="modal-box">
                <h3 class="font-bold text-lg">Suspend User</h3>
                <p class="py-4">
                    Are you sure you want to suspend {{ $user->name }}? This will prevent them from accessing their account.
                </p>
                <div class="modal-action">
                    <form method="POST" action="{{ route('admin.users.suspend', $user) }}">
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
                    Are you sure you want to impersonate {{ $user->name }}? You will be logged in as this user. Use the admin panel to stop impersonating.
                </p>
                <div class="modal-action">
                    <form method="POST" action="{{ route('admin.users.impersonate', $user) }}">
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

