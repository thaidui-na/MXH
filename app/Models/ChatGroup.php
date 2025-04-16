<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model đại diện cho một nhóm chat (ChatGroup) trong ứng dụng.
 * Liên kết với bảng 'chat_groups' trong database.
 */
class ChatGroup extends Model
{
    /**
     * Các thuộc tính có thể được gán hàng loạt (mass assignable).
     * Bảo vệ chống lại mass assignment vulnerability.
     * Chỉ các trường được liệt kê ở đây mới có thể được gán giá trị khi dùng phương thức `create()` hoặc `update()` với một mảng dữ liệu.
     *
     * @var array
     */
    protected $fillable = [
        'name',         // Tên của nhóm chat
        'description',  // Mô tả về nhóm chat (có thể null)
        'created_by',   // ID của người dùng đã tạo nhóm (khóa ngoại tới bảng users)
        'avatar'        // Đường dẫn tới file avatar của nhóm (có thể null)
    ];

    /**
     * Định nghĩa quan hệ một-nhiều (ngược): Một nhóm chat thuộc về một người tạo (User).
     * Liên kết đến User model thông qua khóa ngoại 'created_by'.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        // `belongsTo` xác định quan hệ ngược của `hasMany` hoặc `hasOne`.
        // Tham số thứ hai ('created_by') là tên cột khóa ngoại trong bảng 'chat_groups'.
        // Laravel sẽ mặc định tìm user có id bằng giá trị của cột 'created_by'.
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Định nghĩa quan hệ nhiều-nhiều: Một nhóm chat có nhiều thành viên (User), và một User có thể tham gia nhiều nhóm chat.
     * Liên kết thông qua bảng trung gian 'chat_group_members'.
     * Lấy kèm thông tin từ cột 'is_admin' trong bảng trung gian.
     * Tự động quản lý timestamps ('created_at', 'updated_at') trong bảng trung gian.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function members()
    {
        // `belongsToMany` xác định quan hệ nhiều-nhiều.
        // Tham số thứ nhất: Model liên quan (User).
        // Tham số thứ hai: Tên bảng trung gian (pivot table).
        // Tham số thứ ba: Tên khóa ngoại trong bảng trung gian liên kết đến model hiện tại (ChatGroup).
        // Tham số thứ tư: Tên khóa ngoại trong bảng trung gian liên kết đến model liên quan (User).
        return $this->belongsToMany(User::class, 'chat_group_members', 'group_id', 'user_id')
                    // `withPivot` cho phép truy cập các cột bổ sung trong bảng trung gian.
                    ->withPivot('is_admin')
                    // `withTimestamps` tự động cập nhật cột created_at và updated_at trong bảng trung gian khi thêm/xóa thành viên.
                    ->withTimestamps();
    }

    /**
     * Định nghĩa quan hệ một-nhiều: Một nhóm chat có nhiều tin nhắn (GroupMessage).
     * Liên kết đến GroupMessage model thông qua khóa ngoại 'group_id'.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages()
    {
        // `hasMany` xác định quan hệ một-nhiều.
        // Tham số thứ nhất: Model liên quan (GroupMessage).
        // Tham số thứ hai: Tên cột khóa ngoại trong bảng của model liên quan ('group_messages').
        // Laravel sẽ mặc định tìm các GroupMessage có 'group_id' bằng 'id' của ChatGroup hiện tại.
        return $this->hasMany(GroupMessage::class, 'group_id');
    }

    /**
     * Phương thức helper để kiểm tra xem nhóm có đủ số lượng thành viên tối thiểu hay không.
     * (Ví dụ: yêu cầu nhóm phải có ít nhất 3 thành viên).
     *
     * @return bool True nếu số lượng thành viên lớn hơn hoặc bằng 3, ngược lại là False.
     */
    public function hasMinimumMembers()
    {
        // Đếm số lượng thành viên liên kết với nhóm này thông qua relationship 'members'.
        // So sánh với giá trị tối thiểu (ở đây là 3).
        return $this->members()->count() >= 3;
    }
}