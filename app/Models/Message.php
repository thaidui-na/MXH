<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'content',
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
} 