<header class="bg-base-100 border-b border-base-300 h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8">
    <div class="flex items-center space-x-4">
        <!-- Mobile menu button -->
        <button @click="toggleSidebar()" class="lg:hidden p-2 rounded-md text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500" style="min-width: 44px; min-height: 44px; display: flex; align-items: center; justify-content: center;">
            <svg class="h-6 w-6 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="stroke-width: 2;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <!-- Logo (visible when sidebar is closed) -->
        <div x-show="!sidebarOpen" class="hidden lg:block">
            <a href="{{ route('athlete.dashboard') }}">
                <x-athletegum-logo size="sm" text-color="default" />
            </a>
        </div>

        <!-- Desktop sidebar toggle -->
        <button @click="toggleSidebar()" class="hidden lg:flex btn btn-ghost btn-sm btn-square">
            <svg x-show="sidebarOpen" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
            </svg>
            <svg x-show="!sidebarOpen" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
            </svg>
        </button>
    </div>

    <!-- User Menu -->
    <div class="flex items-center space-x-2 sm:space-x-3">
        @if(session('impersonating'))
            <form method="POST" action="{{ route('admin.stop-impersonating') }}" class="inline">
                @csrf
                <button type="submit" class="badge badge-warning gap-2 border-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    Stop Impersonating
                </button>
            </form>
        @endif

        @php
            $unreadCount = 0;
            try {
                if (Auth::guard('athlete')->check() && class_exists(\App\Models\Notification::class)) {
                    $unreadCount = \App\Models\Notification::where('user_type', 'athlete')
                        ->where('athlete_id', Auth::guard('athlete')->id())
                        ->where('is_read', false)
                        ->count();
                }
            } catch (\Exception $e) {
                $unreadCount = 0;
            }
        @endphp

        <!-- Notifications -->
        <div class="dropdown dropdown-end">
            <label tabindex="0" class="btn btn-ghost relative hover:bg-base-200 focus:bg-base-200" style="width: 44px; height: 44px; padding: 0; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                <svg class="flex-shrink-0 text-gray-900" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 22px; height: 22px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                @if($unreadCount > 0)
                    <span class="absolute -top-1 -right-1 block rounded-full bg-primary text-primary-content text-xs flex items-center justify-center font-bold shadow-md" style="width: 20px; height: 20px; font-size: 10px; line-height: 1; padding: 0;">
                        {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                    </span>
                @endif
            </label>
            <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-[1] w-80 p-2 shadow-lg border border-base-300 max-h-96 overflow-y-auto mt-2">
                <li class="menu-title">
                    <span>Notifications</span>
                    @if($unreadCount > 0)
                        <a href="{{ route('athlete.notifications.index') }}" class="text-xs text-primary">View All</a>
                    @endif
                </li>
                @php
                    $recentNotifications = collect();
                    try {
                        if (Auth::guard('athlete')->check() && class_exists(\App\Models\Notification::class)) {
                            $recentNotifications = \App\Models\Notification::where('user_type', 'athlete')
                                ->where('athlete_id', Auth::guard('athlete')->id())
                                ->orderBy('created_at', 'desc')
                                ->take(5)
                                ->get();
                        }
                    } catch (\Exception $e) {
                        $recentNotifications = collect();
                    }
                @endphp
                @forelse($recentNotifications as $notification)
                    <li>
                        <a href="{{ $notification->action_url ?? route('athlete.notifications.index') }}" 
                           class="{{ !$notification->is_read ? 'bg-base-200' : '' }}"
                           onclick="markAsRead({{ $notification->id }})">
                            <div class="flex-1">
                                <div class="font-medium text-sm">{{ $notification->title }}</div>
                                <div class="text-xs text-base-content/60">{{ Str::limit($notification->message, 60) }}</div>
                                <div class="text-xs text-base-content/40 mt-1">{{ $notification->created_at->diffForHumans() }}</div>
                            </div>
                            @if(!$notification->is_read)
                                <div class="w-2 h-2 bg-primary rounded-full"></div>
                            @endif
                        </a>
                    </li>
                @empty
                    <li><span class="text-sm text-base-content/60">No notifications</span></li>
                @endforelse
                @if($recentNotifications->count() > 0)
                    <li class="border-t border-base-300 mt-2 pt-2">
                        <a href="{{ route('athlete.notifications.index') }}" class="text-center text-sm text-primary">View All Notifications</a>
                    </li>
                @endif
            </ul>
        </div>

        <div class="dropdown dropdown-end">
            <div tabindex="0" role="button" class="btn btn-ghost btn-sm" style="min-height: 44px;">
                <div class="flex items-center space-x-2 sm:space-x-3">
                    <div class="text-right hidden sm:block">
                        <div class="font-medium">{{ Auth::guard('athlete')->user()->name }}</div>
                        <div class="text-xs text-base-content/60">{{ Auth::guard('athlete')->user()->email }}</div>
                    </div>
                    <div class="avatar placeholder">
                        <div class="bg-neutral text-neutral-content rounded-full w-8 sm:w-8">
                            <span class="text-xs">{{ substr(Auth::guard('athlete')->user()->name, 0, 1) }}</span>
                        </div>
                    </div>
                    <svg class="h-4 w-4 text-gray-900 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>
            <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-[1] w-52 p-2 shadow-lg border border-base-300">
                <li><a href="{{ route('athlete.profile.edit') }}">Profile</a></li>
                <li>
                    <form method="POST" action="{{ route('athlete.logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left">Log Out</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>

<script>
    function markAsRead(notificationId) {
        fetch(`/athlete/notifications/${notificationId}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
        }).then(() => {
            // Reload page to update notification count
            window.location.reload();
        });
    }
</script>
