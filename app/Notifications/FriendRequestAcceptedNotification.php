<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class FriendRequestAcceptedNotification extends Notification
{
    use Queueable;

    protected $accepter;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $accepter)
    {
        $this->accepter = $accepter;
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
            'message' => "Bạn đã đồng ý kết bạn với {$this->accepter->name}",
            'avatar' => $this->accepter->avatar_url,
            'user_id' => $this->accepter->id,
            'type' => 'friend_request_accepted',
            'senderName' => $this->accepter->name
        ];
    }
} 