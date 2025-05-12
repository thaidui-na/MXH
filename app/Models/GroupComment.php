<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupComment extends Model
{
    protected $fillable = ['group_post_id', 'user_id', 'content'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function groupPost()
    {
        return $this->belongsTo(GroupPost::class);
    }
}
