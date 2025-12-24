<x-superadmin-dashboard-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Super Admin Dashboard</h2>
    </x-slot>

    <div class="space-y-6">
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-base-content/60">Total Users</p>
                            <p class="text-2xl font-bold mt-1">{{ number_format($stats['total_users']) }}</p>
                        </div>
                        <div class="w-12 h-12 bg-info/10 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-base-content/60">Total Athletes</p>
                            <p class="text-2xl font-bold mt-1">{{ number_format($stats['total_athletes']) }}</p>
                        </div>
                        <div class="w-12 h-12 bg-success/10 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-base-content/60">Active Deals</p>
                            <p class="text-2xl font-bold mt-1">{{ number_format($stats['active_deals']) }}</p>
                            <p class="text-xs text-base-content/60 mt-1">{{ number_format($stats['total_deals']) }} total</p>
                        </div>
                        <div class="w-12 h-12 bg-warning/10 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-base-content/60">Platform Fees</p>
                            <p class="text-2xl font-bold mt-1">${{ number_format($stats['total_platform_fees'], 2) }}</p>
                        </div>
                        <div class="w-12 h-12 bg-secondary/10 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <p class="text-sm font-medium text-base-content/60">Completed Deals</p>
                    <p class="text-2xl font-bold mt-1">{{ number_format($stats['completed_deals']) }}</p>
                </div>
            </div>
            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <p class="text-sm font-medium text-base-content/60">Escrow Held</p>
                    <p class="text-2xl font-bold mt-1">${{ number_format($stats['total_escrow'], 2) }}</p>
                </div>
            </div>
            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <p class="text-sm font-medium text-base-content/60">Pending Withdrawals</p>
                    <p class="text-2xl font-bold mt-1">${{ number_format($stats['pending_withdrawals'], 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Audit Logs -->
            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <h3 class="text-lg font-semibold mb-4">Recent Activity</h3>
                    @if($recentLogs->isEmpty())
                        <p class="text-sm text-base-content/60">No recent activity</p>
                    @else
                        <div class="space-y-4">
                            @foreach($recentLogs as $log)
                                <div class="border-l-4 border-base-300 pl-4">
                                    <p class="text-sm">{{ $log->description }}</p>
                                    <p class="text-xs text-base-content/60 mt-1">
                                        {{ $log->created_at->diffForHumans() }} by {{ $log->admin->name ?? 'System' }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Deals -->
            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold">Recent Deals</h3>
                        <a href="{{ route('admin.deals.index') }}" class="text-sm text-error hover:text-error/80">View all</a>
                    </div>
                    @if($recentDeals->isEmpty())
                        <p class="text-sm text-base-content/60">No recent deals</p>
                    @else
                        <div class="space-y-4">
                            @foreach($recentDeals as $deal)
                                <div class="flex items-center justify-between border-b border-base-300 pb-3 last:border-0 last:pb-0">
                                    <div>
                                        <p class="text-sm font-medium">Deal #{{ $deal->id }}</p>
                                        <p class="text-xs text-base-content/60">{{ $deal->deal_type }} - {{ $deal->user->name ?? 'Unknown' }}</p>
                                    </div>
                                    <span class="badge badge-ghost">
                                        {{ ucfirst($deal->status) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-superadmin-dashboard-layout>
