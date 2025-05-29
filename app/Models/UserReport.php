<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserReport extends Model
{
    use HasFactory;

    protected $table = 'user_user_reports'; // Liên kết đến bảng user_user_reports

    protected $fillable = [
        'reporter_id',
        'reported_user_id',
        'reason',
        'is_resolved',
        'admin_note',
    ];

    protected $casts = [
        'is_resolved' => 'boolean',
    ];

    // Mối quan hệ: báo cáo này được tạo bởi ai
    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    // Mối quan hệ: báo cáo này là về người dùng nào
    public function reportedUser()
    {
        return $this->belongsTo(User::class, 'reported_user_id');
    }

    // Scope để lấy các báo cáo chưa xử lý
    public function scopeUnresolved($query)
    {
        return $query->where('is_resolved', false);
    }

    // Scope để lấy các báo cáo đã xử lý
    public function scopeResolved($query)
    {
        return $query->where('is_resolved', true);
    }
} 