<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model đại diện cho một tin nhắn 1-1 giữa hai người dùng (Message).
 * Liên kết với bảng 'messages' trong database.
 */
class Message extends Model
{
    /**
     * Các thuộc tính có thể được gán hàng loạt (mass assignable).
     * Bảo vệ chống lại mass assignment vulnerability.
     * Chỉ các trường được liệt kê ở đây mới có thể được gán giá trị khi dùng `create()` hoặc `update()` với mảng dữ liệu.
     *
     * @var array
     */
    protected $fillable = [
        'sender_id',    // ID của người dùng gửi tin nhắn (khóa ngoại tới 'users')
        'receiver_id',  // ID của người dùng nhận tin nhắn (khóa ngoại tới 'users')
        'content',      // Nội dung text của tin nhắn (có thể null)
        'image_path',   // Đường dẫn tới file hình ảnh đính kèm (có thể null)
        'sticker',      // Mã hoặc đường dẫn của sticker (có thể null)
        'emoji',        // Ký tự emoji (có thể null)
        'is_read'       // Trạng thái đã đọc (boolean, mặc định thường là false)
    ];

    /**
     * Định nghĩa quan hệ một-nhiều (ngược): Một tin nhắn được gửi bởi một người dùng (User).
     * Liên kết đến User model thông qua khóa ngoại 'sender_id'.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sender()
    {
        // `belongsTo` xác định quan hệ ngược của `hasMany`.
        // Tham số thứ nhất: Model liên quan (User).
        // Tham số thứ hai: Tên cột khóa ngoại trong bảng 'messages'.
        // Laravel sẽ mặc định tìm User có id bằng giá trị của cột 'sender_id'.
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Định nghĩa quan hệ một-nhiều (ngược): Một tin nhắn được nhận bởi một người dùng (User).
     * Liên kết đến User model thông qua khóa ngoại 'receiver_id'.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function receiver()
    {
        // `belongsTo` xác định quan hệ ngược của `hasMany`.
        // Tham số thứ nhất: Model liên quan (User).
        // Tham số thứ hai: Tên cột khóa ngoại trong bảng 'messages'.
        // Laravel sẽ mặc định tìm User có id bằng giá trị của cột 'receiver_id'.
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Phương thức helper để kiểm tra xem tin nhắn có chứa hình ảnh hay không.
     *
     * @return bool True nếu có đường dẫn hình ảnh, ngược lại là False.
     */
    public function hasImage()
    {
        // Kiểm tra xem thuộc tính 'image_path' có giá trị khác null hay không.
        return !is_null($this->image_path);
    }

    /**
     * Phương thức helper để kiểm tra xem tin nhắn có chứa sticker hay không.
     *
     * @return bool True nếu có thông tin sticker, ngược lại là False.
     */
    public function hasSticker()
    {
        // Kiểm tra xem thuộc tính 'sticker' có giá trị khác null hay không.
        return !is_null($this->sticker);
    }

    /**
     * Phương thức helper để lấy URL đầy đủ của hình ảnh đính kèm.
     * Sử dụng hàm `asset()` để tạo URL dựa trên storage link.
     *
     * @return string|null URL của hình ảnh hoặc null nếu không có ảnh.
     */
    public function getImageUrl()
    {
        // Gọi hàm `hasImage()` để kiểm tra.
        // Nếu có ảnh, sử dụng `asset()` để tạo URL tới file trong thư mục 'storage' (đã được liên kết từ 'public/storage').
        // Nếu không có ảnh, trả về null.
        return $this->hasImage() ? asset('storage/' . $this->image_path) : null;
    }

    /**
     * Phương thức helper để lấy URL đầy đủ của sticker.
     * Giả định sticker được lưu trong thư mục public/stickers.
     *
     * @return string|null URL của sticker hoặc null nếu không có sticker.
     */
    public function getStickerUrl()
    {
        // Gọi hàm `hasSticker()` để kiểm tra.
        // Nếu có sticker, sử dụng `asset()` để tạo URL tới file sticker trong thư mục 'public/stickers/'.
        // Nếu không có sticker, trả về null.
        return $this->hasSticker() ? asset('stickers/' . $this->sticker) : null;
    }
} 