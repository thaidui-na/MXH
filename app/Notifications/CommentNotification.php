<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Comment;
use App\Models\User;

class CommentNotification extends Notification
{
    use Queueable;

    protected $comment;
    protected $user;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user, Comment $comment)
    {
        $this->user = $user;
        $this->comment = $comment;
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
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'avatar' => $this->user->avatar_url,
            'post_id' => $this->comment->post_id,
            'comment_id' => $this->comment->id,
            'message' => $this->user->name . ' đã bình luận bài viết của bạn: "' . substr($this->comment->content, 0, 50) . (strlen($this->comment->content) > 50 ? '...' : '') . '"',
            'type' => 'comment'
        ];
    }
} 