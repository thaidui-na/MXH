<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    /**
     * Các trường có thể gán hàng loạt
     */
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'is_public'
    ];

    /**
     * Định nghĩa quan hệ với User
     * Mỗi bài viết thuộc về một user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Một số truy vấn phổ biến có thể sử dụng
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Định nghĩa quan hệ với Comment
     * Một bài viết có thể có nhiều bình luận
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
} 