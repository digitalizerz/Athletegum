<x-athlete-dashboard-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">Dashboard</h2>
    </x-slot>

    @if(session('success'))
        <div role="alert" class="alert alert-success mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-base-content/60">Total Deals</p>
                        <p class="text-2xl font-bold mt-1">{{ $totalDeals }}</p>
                    </div>
                    <div class="h-12 w-12 bg-primary/10 rounded-lg flex items-center justify-center">
                        <svg class="h-6 w-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-base-content/60">Completed</p>
                        <p class="text-2xl font-bold mt-1">{{ $totalCompleted }}</p>
                    </div>
                    <div class="h-12 w-12 bg-success/10 rounded-lg flex items-center justify-center">
                        <svg class="h-6 w-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-base-content/60">Pending</p>
                        <p class="text-2xl font-bold mt-1">{{ $pendingCount }}</p>
                    </div>
                    <div class="h-12 w-12 bg-warning/10 rounded-lg flex items-center justify-center">
                        <svg class="h-6 w-6 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-base-content/60">Total Earnings</p>
                        <p class="text-2xl font-bold mt-1">${{ number_format($totalEarnings, 2) }}</p>
                    </div>
                    <div class="h-12 w-12 bg-success/10 rounded-lg flex items-center justify-center">
                        <svg class="h-6 w-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Profile Link Section -->
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
                <h2 class="text-lg font-semibold mb-4">Your Profile Link</h2>
                <div class="flex items-center gap-2 mb-4">
                    <input
                        type="text"
                        id="profile-url"
                        value="{{ $athlete->profile_url }}"
                        readonly
                        class="input input-bordered flex-1 bg-base-200"
                    />
                    <button
                        type="button"
                        onclick="copyProfileLink()"
                        class="btn btn-primary"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        Copy Link
                    </button>
                    <a
                        href="{{ $athlete->profile_url }}"
                        target="_blank"
                        class="btn btn-outline"
                    >
                        View Profile
                    </a>
                </div>
                <div id="copy-success" role="alert" class="alert alert-success hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Link copied to clipboard!</span>
                </div>
            </div>
        </div>

        <!-- Profile Status -->
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
                <h2 class="text-lg font-semibold mb-4">Profile Status</h2>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-base-content/60">Profile Status</span>
                        <span class="badge badge-success">Live</span>
                    </div>
                    @if($athlete->athlete_level)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-base-content/60">Level</span>
                            <span class="text-sm font-medium capitalize">{{ $athlete->athlete_level }}</span>
                        </div>
                    @endif
                    @if($athlete->sport)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-base-content/60">Sport</span>
                            <span class="text-sm font-medium">{{ $athlete->sport }}</span>
                        </div>
                    @endif
                </div>
                <div class="mt-4">
                    <a href="{{ route('athlete.profile.edit') }}" class="btn btn-outline btn-sm">Edit Profile</a>
                </div>
            </div>
        </div>
    </div>

    <!-- My Deals Section -->
    <div class="mt-6">
        <h2 class="text-xl font-semibold mb-4">My Deals</h2>
        
        <!-- Active Deals (Accepted - Need to Complete) -->
        @if($acceptedDeals->count() > 0)
        <div class="mt-6 card bg-base-100 shadow-sm">
            <div class="card-body">
                <h2 class="text-lg font-semibold mb-4">Active Deals</h2>
                <p class="text-sm text-base-content/60 mb-4">These deals are waiting for you to complete and submit deliverables.</p>
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Business</th>
                                <th>Deal Type</th>
                                <th>Compensation</th>
                                <th>Deadline</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($acceptedDeals as $deal)
                                <tr>
                                    <td class="font-medium">
                                        {{ $deal->user->business_name ?? $deal->user->name ?? 'Business' }}
                                    </td>
                                    <td class="text-sm text-base-content/60">
                                        {{ \App\Models\Deal::getDealTypes()[$deal->deal_type]['name'] ?? ucfirst(str_replace('_', ' ', $deal->deal_type)) }}
                                    </td>
                                    <td>
                                        ${{ number_format($deal->compensation_amount, 2) }}
                                    </td>
                                    <td class="text-sm text-base-content/60">
                                        {{ $deal->deadline ? $deal->deadline->format('M j, Y') : 'N/A' }}
                                    </td>
                                    <td>
                                        <a href="{{ route('athlete.deals.submit.show', $deal) }}" class="btn btn-primary btn-sm">
                                            Submit Deliverables
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @else
            <div class="card bg-base-100 shadow-sm mb-6">
                <div class="card-body">
                    <h3 class="text-lg font-semibold mb-2">Active Deals</h3>
                    <p class="text-sm text-base-content/60">No active deals. Accepted deals will appear here for you to complete.</p>
                </div>
            </div>
        @endif

        <!-- Pending Deals (Not Yet Accepted) -->
        @if($pendingDeals->count() > 0)
        <div class="mt-6 card bg-base-100 shadow-sm">
            <div class="card-body">
                <h2 class="text-lg font-semibold mb-4">Pending Acceptance</h2>
                <p class="text-sm text-base-content/60 mb-4">These deals are waiting for your acceptance.</p>
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Business</th>
                                <th>Deal Type</th>
                                <th>Compensation</th>
                                <th>Deadline</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingDeals as $deal)
                                <tr>
                                    <td class="font-medium">
                                        {{ $deal->user->business_name ?? $deal->user->name ?? 'Business' }}
                                    </td>
                                    <td class="text-sm text-base-content/60">
                                        {{ \App\Models\Deal::getDealTypes()[$deal->deal_type]['name'] ?? ucfirst(str_replace('_', ' ', $deal->deal_type)) }}
                                    </td>
                                    <td>
                                        ${{ number_format($deal->compensation_amount, 2) }}
                                    </td>
                                    <td class="text-sm text-base-content/60">
                                        {{ $deal->deadline ? $deal->deadline->format('M j, Y') : 'N/A' }}
                                    </td>
                                    <td>
                                        <a href="{{ route('deals.show.token', $deal->token) }}" class="btn btn-outline btn-sm">
                                            View Deal
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @else
            <div class="card bg-base-100 shadow-sm mb-6">
                <div class="card-body">
                    <h3 class="text-lg font-semibold mb-2">Pending Acceptance</h3>
                    <p class="text-sm text-base-content/60">No pending deals. Deals shared with you will appear here for acceptance.</p>
                </div>
            </div>
        @endif

        <!-- Completed Deals -->
        @if($completedDeals->count() > 0)
        <div class="mt-6 card bg-base-100 shadow-sm">
            <div class="card-body">
                <h2 class="text-lg font-semibold mb-4">Recent Completed Deals</h2>
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Business</th>
                                <th>Deal Type</th>
                                <th>Compensation</th>
                                <th>Completed</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($completedDeals as $deal)
                                <tr>
                                    <td class="font-medium">
                                        {{ $deal->user->business_name ?? $deal->user->name ?? 'Business' }}
                                    </td>
                                    <td class="text-sm text-base-content/60">
                                        {{ \App\Models\Deal::getDealTypes()[$deal->deal_type]['name'] ?? ucfirst(str_replace('_', ' ', $deal->deal_type)) }}
                                    </td>
                                    <td>
                                        ${{ number_format($deal->compensation_amount, 2) }}
                                    </td>
                                    <td class="text-sm text-base-content/60">
                                        {{ $deal->released_at ? $deal->released_at->format('M j, Y') : ($deal->updated_at->format('M j, Y')) }}
                                    </td>
                                    <td>
                                        <span class="badge badge-success">Completed</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @else
            <div class="card bg-base-100 shadow-sm mb-6">
                <div class="card-body">
                    <h3 class="text-lg font-semibold mb-2">Completed Deals</h3>
                    <p class="text-sm text-base-content/60">No completed deals yet. Completed deals will appear here after you submit deliverables and they're approved.</p>
                </div>
            </div>
        @endif

        <!-- Empty State (only show if no deals at all) -->
        @if($totalDeals === 0)
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body text-center py-12">
                    <p class="text-sm text-base-content/60 mb-4">No deals yet. Share your profile to get started!</p>
                    <div class="text-xs text-base-content/60 space-y-1">
                        <p>• Add to your Instagram bio</p>
                        <p>• Include in your email signature</p>
                        <p>• Share on social media</p>
                        <p>• Send to brands you want to work with</p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script>
        function copyProfileLink() {
            const urlInput = document.getElementById('profile-url');
            urlInput.select();
            urlInput.setSelectionRange(0, 99999);
            
            navigator.clipboard.writeText(urlInput.value).then(function() {
                const successMsg = document.getElementById('copy-success');
                successMsg.classList.remove('hidden');
                setTimeout(() => {
                    successMsg.classList.add('hidden');
                }, 3000);
            });
        }
    </script>
</x-athlete-dashboard-layout>
