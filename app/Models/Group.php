<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = ['user_id', 'name', 'description', 'image', 'privacy'];

    // Một nhóm có nhiều bài viết
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    // Một nhóm thuộc về một người dùng (người tạo)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //
    public function members()
    {
        return $this->belongsToMany(User::class, 'group_user');
    }
    
    

}
