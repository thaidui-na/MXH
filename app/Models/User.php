<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'phone',
        'bio',
        'birthday'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birthday' => 'date',
        ];
    }

    // Accessor để lấy URL avatar
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        // Avatar mặc định nếu chưa upload
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }

    /**
     * Quan hệ một-nhiều với Post
     * Một user có thể có nhiều bài viết
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Lấy tin nhắn đã gửi
     */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Lấy tin nhắn đã nhận
     */
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    /**
     * Lấy số tin nhắn chưa đọc
     */
    public function unreadMessages()
    {
        return $this->receivedMessages()->where('is_read', false);
    }

    /**
     * Lấy số tin nhắn chưa đọc từ một người dùng cụ thể
     */
    public function getUnreadMessagesFrom($senderId)
    {
        return $this->receivedMessages()
            ->where('sender_id', $senderId)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Lấy tin nhắn cuối cùng với một người dùng
     */
    public function getLastMessageWith($userId)
    {
        return Message::where(function($query) use ($userId) {
            $query->where('sender_id', $this->id)
                  ->where('receiver_id', $userId);
        })->orWhere(function($query) use ($userId) {
            $query->where('sender_id', $userId)
                  ->where('receiver_id', $this->id);
        })->latest()->first();
    }
}
