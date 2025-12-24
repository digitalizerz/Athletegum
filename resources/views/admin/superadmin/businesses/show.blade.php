<x-superadmin-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold">Business Details</h2>
            <a href="{{ route('admin.businesses.index') }}" class="btn btn-ghost btn-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Businesses
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Business Info Card -->
        <div class="card bg-base-100 shadow-sm border border-base-300">
            <div class="card-body">
                <h3 class="card-title mb-4">Business Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="label">
                            <span class="label-text font-medium">Name</span>
                        </label>
                        <p class="text-base-content">{{ $user->name }}</p>
                    </div>
                    <div>
                        <label class="label">
                            <span class="label-text font-medium">Email</span>
                        </label>
                        <p class="text-base-content">{{ $user->email }}</p>
                    </div>
                    @if($user->business_name)
                        <div>
                            <label class="label">
                                <span class="label-text font-medium">Business Name</span>
                            </label>
                            <p class="text-base-content">{{ $user->business_name }}</p>
                        </div>
                    @endif
                    @if($user->phone)
                        <div>
                            <label class="label">
                                <span class="label-text font-medium">Phone</span>
                            </label>
                            <p class="text-base-content">{{ $user->phone }}</p>
                        </div>
                    @endif
                    <div>
                        <label class="label">
                            <span class="label-text font-medium">Status</span>
                        </label>
                        <p>
                            @if($user->email_verified_at)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-error">Suspended</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="label">
                            <span class="label-text font-medium">Joined</span>
                        </label>
                        <p class="text-base-content">{{ $user->created_at->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <label class="label">
                            <span class="label-text font-medium">Total Deals</span>
                        </label>
                        <p class="text-base-content">{{ $user->deals_count }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions Card -->
        <div class="card bg-base-100 shadow-sm border border-base-300">
            <div class="card-body">
                <h3 class="card-title mb-4">Actions</h3>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.users.impersonate', $user) }}" class="btn btn-primary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Impersonate
                    </a>
                    @if($user->email_verified_at)
                        <form method="POST" action="{{ route('admin.users.suspend', $user) }}" class="inline">
                            @csrf
                            <button type="submit" class="btn btn-warning">Suspend</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('admin.users.reactivate', $user) }}" class="inline">
                            @csrf
                            <button type="submit" class="btn btn-success">Reactivate</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Deals Card -->
        <div class="card bg-base-100 shadow-sm border border-base-300">
            <div class="card-body">
                <h3 class="card-title mb-4">Recent Deals</h3>
                @if($deals->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Deal</th>
                                    <th>Athlete</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($deals as $deal)
                                    <tr>
                                        <td>
                                            <div class="font-medium">{{ ucfirst(str_replace('_', ' ', $deal->deal_type)) }}</div>
                                        </td>
                                        <td>
                                            {{ $deal->athlete->name ?? 'Unassigned' }}
                                        </td>
                                        <td>${{ number_format($deal->compensation_amount, 2) }}</td>
                                        <td>
                                            <span class="badge badge-{{ $deal->status === 'completed' ? 'success' : ($deal->status === 'cancelled' ? 'error' : 'warning') }}">
                                                {{ ucfirst($deal->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $deal->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="{{ route('admin.deals.show', $deal) }}" class="btn btn-ghost btn-xs btn-square" title="View">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($deals->hasPages())
                        <div class="mt-4">
                            {{ $deals->links() }}
                        </div>
                    @endif
                @else
                    <p class="text-base-content/60">No deals found.</p>
                @endif
            </div>
        </div>
    </div>
</x-superadmin-dashboard-layout>

