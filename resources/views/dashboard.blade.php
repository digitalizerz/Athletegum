<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-semibold">Dashboard</h2>
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

    <div class="space-y-6">
        <!-- Welcome Card -->
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
                <h3 class="text-lg font-semibold mb-2">Welcome back, {{ Auth::user()->name }}!</h3>
                <p class="text-base-content/60">Get started by creating a new deal or managing your existing ones.</p>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <a href="{{ route('deals.create') }}" class="card bg-base-100 shadow-sm hover:shadow-md transition-shadow border border-base-300">
                <div class="card-body">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-primary/10 rounded-lg p-3">
                            <svg class="h-6 w-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium">Create Deal</h3>
                            <p class="text-sm text-base-content/60">Start a new NIL deal</p>
                        </div>
                    </div>
                </div>
            </a>

            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-success/10 rounded-lg p-3">
                            <svg class="h-6 w-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium">Active Deals</h3>
                            <p class="text-sm text-base-content/60">0 deals in progress</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-info/10 rounded-lg p-3">
                            <svg class="h-6 w-6 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium">Total Paid</h3>
                            <p class="text-sm text-base-content/60">$0.00</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
                <h3 class="text-lg font-semibold mb-4">Recent Deals</h3>
                <p class="text-sm text-base-content/60 text-center py-8">No deals yet. Create your first deal to get started.</p>
            </div>
        </div>
    </div>
</x-app-layout>
