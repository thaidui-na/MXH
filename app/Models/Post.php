<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model đại diện cho một bài viết (Post) trong ứng dụng.
 * Liên kết với bảng 'posts' trong database.
 */
class Post extends Model
{
    use HasFactory;

    /**
     * Các thuộc tính có thể được gán hàng loạt (mass assignable).
     * Bảo vệ chống lại mass assignment vulnerability.
     * Chỉ các trường được liệt kê ở đây mới có thể được gán giá trị khi dùng `create()` hoặc `update()` với mảng dữ liệu.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',    // ID của người dùng đã đăng bài viết (khóa ngoại tới 'users')
        'title',      // Tiêu đề của bài viết
        'content',    // Nội dung của bài viết
        'is_public'   // Trạng thái công khai/riêng tư của bài viết (boolean)
    ];

    /**
     * Định nghĩa quan hệ một-nhiều (ngược): Một bài viết thuộc về một người dùng (User).
     * Liên kết đến User model thông qua khóa ngoại 'user_id'.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        // `belongsTo` xác định quan hệ ngược của `hasMany`.
        // Tham số thứ nhất: Model liên quan (User).
        // Laravel sẽ tự động xác định khóa ngoại dựa trên tên phương thức ('user' -> 'user_id').
        // Nếu tên khóa ngoại khác, bạn cần truyền nó làm tham số thứ hai.
        return $this->belongsTo(User::class);
    }

    /**
     * Định nghĩa một Local Query Scope để lọc các bài viết công khai.
     * Cho phép tái sử dụng logic query này một cách dễ dàng.
     * Cách dùng: Post::public()->get();
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query Instance của Query Builder
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublic($query)
    {
        // Thêm điều kiện `where` vào query builder để chỉ lấy các bài viết có 'is_public' là true.
        return $query->where('is_public', true);
    }

    /**
     * Định nghĩa một Local Query Scope để sắp xếp bài viết theo thứ tự mới nhất.
     * Cách dùng: Post::latest()->get();
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query Instance của Query Builder
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLatest($query)
    {
        // Thêm điều kiện `orderBy` vào query builder để sắp xếp theo cột 'created_at' giảm dần (mới nhất trước).
        // Lưu ý: Laravel cũng cung cấp sẵn phương thức `latest()` hoạt động tương tự.
        return $query->orderBy('created_at', 'desc');
    }
    public function likes() {
        return $this->hasMany(Like::class);
    }
    
    public function isLikedBy($user) {
        return $this->likes()->where('user_id', $user->id)->exists();
    }
    
    public function likesCount() {
        return $this->likes()->count();
    }
    
} 