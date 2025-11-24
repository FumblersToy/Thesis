<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;
use App\Models\User;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get conversations list
     */
    public function index()
    {
        $conversations = Message::getRecentConversations(Auth::id());
        
        // Add is_online status to each conversation
        $conversations = $conversations->map(function($conversation) {
            $conversation['is_online'] = $conversation['user']->isOnline();
            return $conversation;
        });
        
        return response()->json([
            'success' => true,
            'conversations' => $conversations
        ]);
    }

    /**
     * Get conversation with a specific user
     */
    public function show(Request $request, $userId)
    {
        $user = User::with(['musician', 'business'])->findOrFail($userId);
        $messages = Message::getConversation(Auth::id(), $userId);
        
        // Mark messages as read if there are any
        if ($messages->count() > 0) {
            Message::where('sender_id', $userId)
                   ->where('receiver_id', Auth::id())
                   ->where('is_read', false)
                   ->update([
                       'is_read' => true,
                       'read_at' => now()
                   ]);
        }

        return response()->json([
            'success' => true,
            'user' => $user,
            'is_online' => $user->isOnline(),
            'messages' => $messages
        ]);
    }

    /**
     * Send a message
     */
    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'content' => 'required|string|max:1000',
        ]);

        // Prevent sending messages to yourself
        if ($request->receiver_id == Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot send messages to yourself.'
            ], 400);
        }

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'content' => $request->content,
        ]);

        // Load relationships
        $message->load(['sender.musician', 'sender.business', 'receiver.musician', 'receiver.business']);

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    /**
     * Mark messages as read
     */
    public function markAsRead(Request $request, $userId)
    {
        Message::where('sender_id', $userId)
               ->where('receiver_id', Auth::id())
               ->where('is_read', false)
               ->update([
                   'is_read' => true,
                   'read_at' => now()
               ]);

        return response()->json([
            'success' => true,
            'message' => 'Messages marked as read'
        ]);
    }

    /**
     * Get unread message count
     */
    public function unreadCount()
    {
        $count = Auth::user()->unreadMessageCount();

        return response()->json([
            'success' => true,
            'unread_count' => $count
        ]);
    }

    /**
     * Search users for messaging
     */
    public function searchUsers(Request $request)
    {
        $query = $request->get('query', '');
        
        if (strlen($query) < 2) {
            return response()->json([
                'success' => true,
                'users' => []
            ]);
        }

        $users = User::with(['musician', 'business'])
                    ->where('id', '!=', Auth::id())
                    ->where(function ($q) use ($query) {
                        $q->where('name', 'like', "%{$query}%")
                          ->orWhere('email', 'like', "%{$query}%")
                          ->orWhereHas('musician', function ($mq) use ($query) {
                              $mq->where('stage_name', 'like', "%{$query}%");
                          })
                          ->orWhereHas('business', function ($bq) use ($query) {
                              $bq->where('business_name', 'like', "%{$query}%");
                          });
                    })
                    ->limit(10)
                    ->get();

        return response()->json([
            'success' => true,
            'users' => $users
        ]);
    }
}
