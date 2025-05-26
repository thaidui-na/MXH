<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'event_time',
        'location',
        'image_path'
    ];

    protected $casts = [
        'event_time' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_user')
                    ->withPivot('status', 'joined_at', 'left_at')
                    ->withTimestamps();
    }

    public function activeParticipants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_user')
                    ->wherePivot('status', 'joined')
                    ->withPivot('joined_at')
                    ->withTimestamps();
    }

    public function isParticipant(User $user)
    {
        return $this->activeParticipants()->where('user_id', $user->id)->exists();
    }

    public function getParticipantsCountAttribute()
    {
        return $this->activeParticipants()->count();
    }

    protected function getJoinedAtAttribute($value)
    {
        return $value ? \Carbon\Carbon::parse($value) : null;
    }

    protected function getLeftAtAttribute($value)
    {
        return $value ? \Carbon\Carbon::parse($value) : null;
    }
}
