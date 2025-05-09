<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupMessage extends Model
{
    protected $fillable = [
        'group_id',
        'sender_id',
        'content'
    ];

    /**
     * Quan hệ với nhóm chat
     */
    public function group()
    {
        return $this->belongsTo(ChatGroup::class, 'group_id');
    }

    /**
     * Quan hệ với người gửi
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
