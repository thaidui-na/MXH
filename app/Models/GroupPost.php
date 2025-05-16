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

    public function likes()
    {
        return $this->belongsToMany(User::class, 'group_post_likes', 'group_post_id', 'user_id');
    }

    public function favorites()
    {
        return $this->belongsToMany(User::class, 'group_post_favorites', 'group_post_id', 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(GroupComment::class, 'post_id');
    }

    public function isLikedBy($userId)
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }

    public function isFavoritedBy($userId)
    {
        return $this->favorites()->where('user_id', $userId)->exists();
    }

    public function getLikesCount()
    {
        return $this->likes()->count();
    }

    public function scopeOrderByFavorites($query)
    {
        return $query->leftJoin('group_post_favorites', 'group_posts.id', '=', 'group_post_favorites.group_post_id')
                    ->select('group_posts.*')
                    ->selectRaw('COUNT(group_post_favorites.id) as favorites_count')
                    ->groupBy(
                        'group_posts.id',
                        'group_posts.group_id',
                        'group_posts.user_id',
                        'group_posts.title',
                        'group_posts.content',
                        'group_posts.image',
                        'group_posts.is_approved',
                        'group_posts.created_at',
                        'group_posts.updated_at'
                    )
                    ->orderByDesc('favorites_count')
                    ->orderByDesc('group_posts.created_at');
    }
}