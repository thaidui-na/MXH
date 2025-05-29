<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class FriendRemovedNotification extends Notification
{
    use Queueable;

    protected $remover;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $remover)
    {
        $this->remover = $remover;
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
            'message' => "{$this->remover->name} đã hủy kết bạn với bạn",
            'avatar' => $this->remover->avatar_url,
            'user_id' => $this->remover->id,
            'type' => 'friend_removed'
        ];
    }
} 