<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;
use App\Models\Post;

class PostLikeNotification extends Notification
{
    use Queueable;

    protected $liker;
    protected $post;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $liker, Post $post)
    {
        $this->liker = $liker;
        $this->post = $post;
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
            'message' => "{$this->liker->name} đã thích bài viết của bạn",
            'avatar' => $this->liker->avatar_url,
            'user_id' => $this->liker->id,
            'post_id' => $this->post->id,
            'type' => 'post_like'
        ];
    }
} 