<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl leading-tight">Notifications</h2>
            @php
                $unreadCount = \App\Models\Notification::where('user_type', 'user')
                    ->where('user_id', Auth::id())
                    ->where('is_read', false)
                    ->count();
            @endphp
            @if($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="btn btn-ghost btn-sm">
                        Mark All as Read
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        @if(session('success'))
            <div role="alert" class="alert alert-success mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($notifications->count() > 0)
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body p-0">
                    <div class="divide-y divide-base-300">
                        @foreach($notifications as $notification)
                            <a href="{{ $notification->action_url ?? '#' }}" 
                               class="block p-4 hover:bg-base-200 transition {{ !$notification->is_read ? 'bg-base-200' : '' }}"
                               onclick="markAsRead({{ $notification->id }})">
                                <div class="flex items-start gap-3">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <h3 class="font-semibold text-sm">{{ $notification->title }}</h3>
                                            @if(!$notification->is_read)
                                                <span class="badge badge-primary badge-xs">New</span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-base-content/70 mb-2">{{ $notification->message }}</p>
                                        <div class="flex items-center gap-4 text-xs text-base-content/50">
                                            <span>{{ $notification->created_at->format('M j, Y g:i A') }}</span>
                                            <span>{{ $notification->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
                
                @if($notifications->hasPages())
                    <div class="card-body border-t border-base-300">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        @else
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-base-content/30 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <p class="text-base-content/60">No notifications</p>
                </div>
            </div>
        @endif
    </div>

    <script>
        function markAsRead(notificationId) {
            fetch(`/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                },
            });
        }
    </script>
</x-app-layout>

