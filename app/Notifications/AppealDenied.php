<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppealDenied extends Notification implements ShouldQueue
{
    use Queueable;

    public $postId;
    public $response;

    /**
     * Create a new notification instance.
     */
    public function __construct($postId, $response = null)
    {
        $this->postId = $postId;
        $this->response = $response;
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
            'type' => 'appeal_denied',
            'post_id' => $this->postId,
            'message' => 'Your appeal has been denied.',
            'admin_response' => $this->response,
        ];
    }
}
