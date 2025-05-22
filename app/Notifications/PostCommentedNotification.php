<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PostCommentedNotification extends Notification
{
    use Queueable;

    protected $commenter;
    protected $post;
    protected $comment_content;

    /**
     * Create a new notification instance.
     */
    public function __construct($commenter, $post, $comment_content)
    {
        $this->commenter = $commenter;
        $this->post = $post;
        $this->comment_content = $comment_content;
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
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'comment',
            'commenter_id' => $this->commenter->id,
            'commenter_name' => $this->commenter->name,
            'post_id' => $this->post->id,
            'post_title' => $this->post->title,
            'comment_content' => $this->comment_content,
            'message' => $this->commenter->name . ' đã bình luận bài viết của bạn: ' . $this->post->title,
            'url' => route('posts.show', $this->post->id)
        ];
    }
}
