<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Story extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'media_path',
        'media_type',
        'caption',
        'expires_at',
        'is_active'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    // Quan hệ với User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessor để lấy URL đầy đủ của media
    public function getMediaUrlAttribute()
    {
        return Storage::url($this->media_path);
    }

    // Scope để lấy các story còn hạn
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where('expires_at', '>', now());
    }

    // Scope để lấy các story của người dùng đang theo dõi
    public function scopeFromFollowing($query, $userId)
    {
        return $query->whereIn('user_id', function($subquery) use ($userId) {
            $subquery->select('following_id')
                    ->from('followers')
                    ->where('follower_id', $userId);
        });
    }
}
