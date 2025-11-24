<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'content',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Mark message as read
     */
    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Get conversation between two users
     */
    public static function getConversation(int $userId1, int $userId2, int $limit = 50)
    {
        return self::where(function ($query) use ($userId1, $userId2) {
            $query->where('sender_id', $userId1)
                  ->where('receiver_id', $userId2);
        })->orWhere(function ($query) use ($userId1, $userId2) {
            $query->where('sender_id', $userId2)
                  ->where('receiver_id', $userId1);
        })
        ->with(['sender.musician', 'sender.business', 'receiver.musician', 'receiver.business'])
        ->orderBy('created_at', 'desc')
        ->limit($limit)
        ->get()
        ->reverse()
        ->values();
    }

    /**
     * Get recent conversations for a user
     */
    public static function getRecentConversations(int $userId, int $limit = 20)
    {
        $conversations = self::selectRaw('
            CASE 
                WHEN sender_id = ? THEN receiver_id 
                ELSE sender_id 
            END as other_user_id,
            MAX(created_at) as last_message_at,
            COUNT(*) as message_count,
            SUM(CASE WHEN receiver_id = ? AND is_read = 0 THEN 1 ELSE 0 END) as unread_count
        ', [$userId, $userId])
        ->where(function ($query) use ($userId) {
            $query->where('sender_id', $userId)
                  ->orWhere('receiver_id', $userId);
        })
        ->groupBy('other_user_id')
        ->orderBy('last_message_at', 'desc')
        ->limit($limit)
        ->get();

        // Load user details for each conversation
        $userIds = $conversations->pluck('other_user_id')->toArray();
        $users = User::with(['musician', 'business'])
                    ->whereIn('id', $userIds)
                    ->get()
                    ->keyBy('id');

        return $conversations->map(function ($conversation) use ($users) {
            $user = $users->get($conversation->other_user_id);
            return [
                'user' => $user,
                'last_message_at' => \Carbon\Carbon::parse($conversation->last_message_at)->toIso8601String(),
                'message_count' => $conversation->message_count,
                'unread_count' => $conversation->unread_count,
            ];
        });
    }
}
