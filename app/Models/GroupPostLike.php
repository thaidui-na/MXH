<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupPostLike extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_post_id',
        'user_id',
    ];

    public function groupPost()
    {
        return $this->belongsTo(GroupPost::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
