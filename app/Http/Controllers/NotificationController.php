<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->with(['notifier.musician', 'notifier.business', 'post'])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return response()->json([
            'notifications' => $notifications->map(function ($notification) {
                // For post_deleted, appeal_approved, appeal_denied notifications, notifier is admin
                $notifierName = null;
                if ($notification->notifier) {
                    if ($notification->notifier->musician) {
                        $notifierName = $notification->notifier->musician->artist_name ?? $notification->notifier->musician->stage_name;
                    } elseif ($notification->notifier->business) {
                        $notifierName = $notification->notifier->business->business_name;
                    }
                }
                
                if (!$notifierName && $notification->notifier) {
                    $notifierName = $notification->notifier->name ?? 'Admin';
                }
                
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'message' => $notification->message,
                    'notifier_name' => $notifierName,
                    'post_id' => $notification->post_id,
                    'read' => $notification->read,
                    'created_at' => $notification->created_at->toIso8601String(),
                ];
            }),
            'unread_count' => Notification::where('user_id', Auth::id())
                ->where('read', false)
                ->count()
        ]);
    }

    public function markAsRead($id)
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if ($notification) {
            $notification->update(['read' => true]);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('read', false)
            ->update(['read' => true]);

        return response()->json(['success' => true]);
    }
}
