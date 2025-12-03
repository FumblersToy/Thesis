<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PostDeleted extends Notification implements ShouldQueue
{
    use Queueable;

    public $postId;
    public $reason;
    public $deletedAt;

    /**
     * Create a new notification instance.
     */
    public function __construct($postId, $reason, $deletedAt)
    {
        $this->postId = $postId;
        $this->reason = $reason;
        $this->deletedAt = $deletedAt;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'post_deleted',
            'post_id' => $this->postId,
            'reason' => $this->reason,
            'deleted_at' => $this->deletedAt,
            'message' => 'Your post has been removed by an admin.',
            'appeal_deadline' => now()->addDays(15)->toDateTimeString(),
        ];
    }
}
