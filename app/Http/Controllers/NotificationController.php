<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display all notifications for SMB
     */
    public function index(Request $request)
    {
        $query = Notification::where('user_type', 'user')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc');

        $notifications = $query->paginate(20);

        return view('notifications.index', [
            'notifications' => $notifications,
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Notification $notification)
    {
        if ($notification->user_type === 'user' && $notification->user_id === Auth::id()) {
            $notification->markAsRead();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 403);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        Notification::where('user_type', 'user')
            ->where('user_id', Auth::id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }
}
