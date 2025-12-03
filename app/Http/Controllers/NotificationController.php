<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get custom notifications (likes, comments)
        $customNotifications = Notification::where('user_id', $user->id)
            ->with(['notifier.musician', 'notifier.business', 'post'])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($notification) {
                $notifierName = $notification->notifier->musician->artist_name 
                    ?? $notification->notifier->business->business_name 
                    ?? 'Someone';
                
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'message' => $notification->message,
                    'notifier_name' => $notifierName,
                    'post_id' => $notification->post_id,
                    'read' => $notification->read,
                    'created_at' => $notification->created_at->toIso8601String(),
                ];
            });

        // Get Laravel database notifications (post deletions, appeals)
        $databaseNotifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($notification) {
                $data = $notification->data;
                return [
                    'id' => $notification->id,
                    'type' => $data['type'] ?? 'notification',
                    'message' => $data['message'] ?? 'You have a new notification',
                    'notifier_name' => null,
                    'post_id' => $data['post_id'] ?? null,
                    'read' => $notification->read_at !== null,
                    'created_at' => $notification->created_at->toIso8601String(),
                ];
            });

        // Merge and sort all notifications
        $allNotifications = $customNotifications->concat($databaseNotifications)
            ->sortByDesc('created_at')
            ->take(50)
            ->values();

        // Count unread
        $unreadCount = Notification::where('user_id', $user->id)
            ->where('read', false)
            ->count();
        
        $unreadCount += $user->unreadNotifications()->count();

        return response()->json([
            'notifications' => $allNotifications,
            'unread_count' => $unreadCount
        ]);
    }

    public function markAsRead($id)
    {
        // Try custom notification first
        $notification = Notification::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if ($notification) {
            $notification->update(['read' => true]);
            return response()->json(['success' => true]);
        }

        // Try Laravel database notification
        $dbNotification = Auth::user()->notifications()->find($id);
        if ($dbNotification) {
            $dbNotification->markAsRead();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }

    public function markAllAsRead()
    {
        // Mark custom notifications as read
        Notification::where('user_id', Auth::id())
            ->where('read', false)
            ->update(['read' => true]);

        // Mark Laravel database notifications as read
        Auth::user()->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }
}
