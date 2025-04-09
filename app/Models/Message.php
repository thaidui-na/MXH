<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'content',
        'image_path',
        'is_read'
    ];

    /**
     * Lấy thông tin người gửi
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Lấy thông tin người nhận
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Kiểm tra tin nhắn có hình ảnh không
     */
    public function hasImage()
    {
        return !is_null($this->image_path);
    }

    /**
     * Lấy URL của hình ảnh
     */
    public function getImageUrl()
    {
        return $this->hasImage() ? asset('storage/' . $this->image_path) : null;
    }
} 