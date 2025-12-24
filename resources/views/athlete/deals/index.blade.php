<x-athlete-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl leading-tight">My Deals</h2>
        </div>
    </x-slot>

    @if(session('success'))
        <div role="alert" class="alert alert-success mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Filters -->
    <div class="card bg-base-100 shadow-sm mb-6">
        <div class="card-body">
            <form method="GET" action="{{ route('athlete.deals.index') }}" class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}"
                        placeholder="Search deals..." 
                        class="input input-bordered w-full"
                    >
                </div>
                <div class="sm:w-48">
                    <select name="status" class="select select-bordered w-full">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>Active</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Search</button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('athlete.deals.index') }}" class="btn btn-ghost">Clear</a>
                @endif
            </form>
        </div>
    </div>

    <!-- Deals Table -->
    <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
            @if($deals->count() > 0)
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Business</th>
                                <th>Deal Type</th>
                                <th>Compensation</th>
                                <th>Deadline</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($deals as $deal)
                                <tr>
                                    <td class="font-medium">
                                        {{ $deal->user->business_name ?? $deal->user->name ?? 'Business' }}
                                    </td>
                                    <td class="text-sm text-base-content/60">
                                        <div class="flex items-center space-x-2">
                                            <span>{{ \App\Models\Deal::getDealTypes()[$deal->deal_type]['icon'] ?? '📋' }}</span>
                                            <span>{{ \App\Models\Deal::getDealTypes()[$deal->deal_type]['name'] ?? ucfirst(str_replace('_', ' ', $deal->deal_type)) }}</span>
                                        </div>
                                    </td>
                                    <td class="font-semibold">
                                        ${{ number_format($deal->compensation_amount, 2) }}
                                    </td>
                                    <td class="text-sm text-base-content/60">
                                        {{ $deal->deadline ? $deal->deadline->format('M j, Y') : 'N/A' }}
                                        @if($deal->deadline_time)
                                            @php
                                                try {
                                                    $time = \Carbon\Carbon::createFromFormat('H:i:s', $deal->deadline_time)->format('g:i A');
                                                } catch (\Exception $e) {
                                                    $time = \Carbon\Carbon::createFromFormat('H:i', $deal->deadline_time)->format('g:i A');
                                                }
                                            @endphp
                                            <div class="text-xs">{{ $time }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($deal->status === 'pending')
                                            <span class="badge badge-warning">Pending</span>
                                        @elseif($deal->status === 'accepted')
                                            <span class="badge badge-info">Active</span>
                                        @elseif($deal->status === 'completed')
                                            @if($deal->is_approved && $deal->released_at)
                                                <span class="badge badge-success">Paid</span>
                                            @else
                                                <span class="badge badge-success">Completed</span>
                                            @endif
                                        @elseif($deal->status === 'cancelled')
                                            <span class="badge badge-error">Cancelled</span>
                                        @else
                                            <span class="badge badge-ghost">{{ ucfirst($deal->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            @if($deal->status === 'pending')
                                                <a href="{{ route('deals.show.token', $deal->token) }}" class="btn btn-sm btn-outline" title="View Deal">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    <span class="sr-only">View</span>
                                                </a>
                                            @elseif($deal->status === 'accepted')
                                                <a href="{{ route('athlete.deals.submit.show', $deal) }}" class="btn btn-sm btn-primary" title="Submit Deliverables">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                    <span class="sr-only">Submit</span>
                                                </a>
                                                <a href="{{ route('athlete.deals.messages', $deal) }}" class="btn btn-sm btn-ghost" title="Messages">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                                    </svg>
                                                    <span class="sr-only">Messages</span>
                                                </a>
                                            @else
                                                <a href="{{ route('athlete.deals.show', $deal) }}" class="btn btn-sm btn-ghost" title="View Details">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    <span class="sr-only">View</span>
                                                </a>
                                                @if($deal->status !== 'pending')
                                                    <a href="{{ route('athlete.deals.messages', $deal) }}" class="btn btn-sm btn-ghost" title="Messages">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                                        </svg>
                                                        <span class="sr-only">Messages</span>
                                                    </a>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="mt-6">
                    {{ $deals->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-base-content/30 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-base-content/60 mb-2">No deals found</p>
                    <p class="text-sm text-base-content/50">
                        @if(request()->hasAny(['search', 'status']))
                            Try adjusting your filters
                        @else
                            Share your profile to get started!
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</x-athlete-dashboard-layout>

