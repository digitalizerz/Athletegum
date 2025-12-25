<aside :class="sidebarOpen ? 'translate-x-0 lg:translate-x-0' : '-translate-x-full lg:-translate-x-full'"
       class="fixed inset-y-0 left-0 z-30 w-64 bg-base-100 border-r border-base-300 transition-transform duration-300 ease-in-out transform lg:fixed">
    <div class="flex flex-col h-full">
        <!-- Logo -->
        <div class="flex items-center justify-between h-16 px-6 border-b border-base-300">
            <a href="{{ route('athlete.dashboard') }}">
                <x-athletegum-logo size="md" text-color="default" />
            </a>
            <button @click="toggleSidebar()" class="lg:hidden btn btn-ghost btn-sm btn-square">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Navigation -->
        <ul class="menu menu-vertical flex-1 px-4 py-6 space-y-1 overflow-y-auto">
            <li>
                <a href="{{ route('athlete.dashboard') }}" 
                   class="{{ request()->routeIs('athlete.dashboard') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>
            </li>

            <li>
                <a href="{{ route('athlete.deals.index') }}" 
                   class="{{ request()->routeIs('athlete.deals.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    My Deals
                </a>
            </li>

            <li>
                <a href="{{ route('athlete.messages.index') }}" 
                   class="{{ request()->routeIs('athlete.messages.*') || request()->routeIs('athlete.deals.messages') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    Messages
                    @php
                        try {
                            $athleteId = Auth::guard('athlete')->id();
                            // Check if the column exists by trying to query it
                            $columns = \Schema::hasColumns('messages', ['read_by_athlete_ids']);
                            
                            if ($columns) {
                                // New read tracking logic
                                $unreadMessageCount = \App\Models\Message::whereHas('deal', function($query) use ($athleteId) {
                                    $query->where('athlete_id', $athleteId)
                                          ->where('status', '!=', 'pending')
                                          ->where('status', '!=', 'completed')
                                          ->where('status', '!=', 'cancelled')
                                          ->whereNull('released_at');
                                })
                                ->where('sender_type', 'user')
                                ->get()
                                ->filter(function($message) use ($athleteId) {
                                    $readBy = $message->read_by_athlete_ids ?? [];
                                    return !in_array($athleteId, $readBy);
                                })
                                ->count();
                            } else {
                                // Fallback: count all messages (no read tracking yet)
                                $unreadMessageCount = \App\Models\Message::whereHas('deal', function($query) use ($athleteId) {
                                    $query->where('athlete_id', $athleteId)
                                          ->where('status', '!=', 'pending')
                                          ->where('status', '!=', 'completed')
                                          ->where('status', '!=', 'cancelled')
                                          ->whereNull('released_at');
                                })
                                ->where('sender_type', 'user')
                                ->count();
                            }
                        } catch (\Exception $e) {
                            $unreadMessageCount = 0;
                        }
                    @endphp
                    @if($unreadMessageCount > 0)
                        <span class="badge badge-primary badge-sm">{{ $unreadMessageCount }}</span>
                    @endif
                </a>
            </li>

            <li>
                <a href="{{ route('athlete.profile.edit') }}" 
                   class="{{ request()->routeIs('athlete.profile.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Profile
                </a>
            </li>

            <li>
                <a href="{{ route('athlete.earnings.index') }}" 
                   class="{{ request()->routeIs('athlete.earnings.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Earnings
                </a>
            </li>
        </ul>
    </div>
</aside>
