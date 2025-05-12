<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'user_id',
        'title',
        'content',
        'image',
        'is_approved'
    ];

    protected $casts = [
        'is_approved' => 'boolean'
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function canEdit($userId)
    {
        return $this->user_id === $userId || 
               $this->group->hasAdmin($userId) || 
               $this->group->hasModerator($userId);
    }

    public function canDelete($userId)
    {
        return $this->user_id === $userId || 
               $this->group->hasAdmin($userId) || 
               $this->group->hasModerator($userId);
    }

    public function comments()
    {
        return $this->hasMany(GroupComment::class);
    }
}