<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatGroup extends Model
{
    protected $fillable = [
        'name',
        'description',
        'created_by',
        'avatar'
    ];

    /**
     * Quan hệ với người tạo nhóm
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Quan hệ với thành viên nhóm
     */
    public function members()
    {
        return $this->belongsToMany(User::class, 'chat_group_members', 'group_id', 'user_id')
                    ->withPivot('is_admin')
                    ->withTimestamps();
    }

    /**
     * Quan hệ với tin nhắn trong nhóm
     */
    public function messages()
    {
        return $this->hasMany(GroupMessage::class, 'group_id');
    }

    /**
     * Kiểm tra số lượng thành viên
     */
    public function hasMinimumMembers()
    {
        return $this->members()->count() >= 3;
    }
}