<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model đại diện cho một tin nhắn trong nhóm chat (GroupMessage).
 * Liên kết với bảng 'group_messages' trong database.
 */
class GroupMessage extends Model
{
    /**
     * Các thuộc tính có thể được gán hàng loạt (mass assignable).
     * Bảo vệ chống lại mass assignment vulnerability.
     * Chỉ các trường được liệt kê ở đây mới có thể được gán giá trị khi dùng `create()` hoặc `update()` với mảng dữ liệu.
     *
     * @var array
     */
    protected $fillable = [
        'group_id',   // ID của nhóm chat mà tin nhắn này thuộc về (khóa ngoại tới 'chat_groups')
        'sender_id',  // ID của người dùng đã gửi tin nhắn (khóa ngoại tới 'users')
        'content'     // Nội dung của tin nhắn
        // Nếu có các trường khác như 'image_path', 'sticker_id', cũng cần thêm vào đây
    ];

    /**
     * Định nghĩa quan hệ một-nhiều (ngược): Một tin nhắn nhóm thuộc về một nhóm chat (ChatGroup).
     * Liên kết đến ChatGroup model thông qua khóa ngoại 'group_id'.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        // `belongsTo` xác định quan hệ ngược của `hasMany`.
        // Tham số thứ nhất: Model liên quan (ChatGroup).
        // Tham số thứ hai: Tên cột khóa ngoại trong bảng 'group_messages'.
        // Laravel sẽ mặc định tìm ChatGroup có id bằng giá trị của cột 'group_id'.
        return $this->belongsTo(ChatGroup::class, 'group_id');
    }

    /**
     * Định nghĩa quan hệ một-nhiều (ngược): Một tin nhắn nhóm được gửi bởi một người dùng (User).
     * Liên kết đến User model thông qua khóa ngoại 'sender_id'.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sender()
    {
        // `belongsTo` xác định quan hệ ngược của `hasMany`.
        // Tham số thứ nhất: Model liên quan (User).
        // Tham số thứ hai: Tên cột khóa ngoại trong bảng 'group_messages'.
        // Laravel sẽ mặc định tìm User có id bằng giá trị của cột 'sender_id'.
        return $this->belongsTo(User::class, 'sender_id');
    }
}
