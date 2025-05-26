<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'event_type',
        'event_time',
        'location',
        'image_path',
        'user_id'
    ];

    protected $casts = [
        'event_time' => 'datetime',
        'event_type' => 'string'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
