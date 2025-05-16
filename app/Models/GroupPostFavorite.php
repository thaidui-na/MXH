<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupPostFavorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'group_post_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(GroupPost::class, 'group_post_id');
    }
}