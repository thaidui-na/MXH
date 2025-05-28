<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

/**
 * Model đại diện cho một người dùng (User) trong hệ thống.
 * Kế thừa từ Authenticatable để hỗ trợ các chức năng đăng nhập, xác thực.
 * Liên kết với bảng 'users' trong database.
 */
class User extends Authenticatable
{
    /**
     * Sử dụng các trait cần thiết.
     * HasFactory: Cho phép sử dụng model factories để tạo dữ liệu mẫu.
     * Notifiable: Cho phép gửi thông báo (notifications) đến người dùng này.
     */
    use HasFactory, Notifiable;

    /**
     * Các thuộc tính có thể được gán hàng loạt (mass assignable).
     * Chỉ các trường trong mảng này mới có thể được gán giá trị khi dùng `User::create([...])` hoặc `$user->update([...])`.
     *
     * @var list<string> // Gợi ý kiểu: một danh sách các chuỗi
     */
    protected $fillable = [
        'name',         // Tên của người dùng
        'email',        // Địa chỉ email (thường dùng để đăng nhập)
        'password',     // Mật khẩu (sẽ được hash tự động nhờ $casts)
        'avatar',       // Đường dẫn tới file avatar của người dùng (có thể null)
        'phone',        // Số điện thoại của người dùng (có thể null)
        'bio',          // Giới thiệu ngắn về người dùng (có thể null)
        'birthday',     // Ngày sinh của người dùng (có thể null)
        'account_status' // Trạng thái tài khoản
    ];

    /**
     * Các thuộc tính sẽ bị ẩn khi model được chuyển đổi thành mảng hoặc JSON.
     * Dùng để bảo mật, không trả về các thông tin nhạy cảm như password.
     *
     * @var list<string> // Gợi ý kiểu: một danh sách các chuỗi
     */
    protected $hidden = [
        'password',        // Ẩn mật khẩu đã hash
        'remember_token', // Ẩn token dùng cho chức năng "Remember Me"
    ];

    /**
     * Định nghĩa cách các thuộc tính sẽ được ép kiểu (cast) khi truy cập hoặc gán giá trị.
     * Giúp xử lý dữ liệu một cách tự động và nhất quán.
     *
     * @return array<string, string> // Gợi ý kiểu: mảng map tên thuộc tính -> kiểu dữ liệu
     */
    protected $casts = [
        // Ép kiểu 'email_verified_at' thành đối tượng Carbon (ngày giờ) khi truy cập
            'email_verified_at' => 'datetime',
        // Tự động hash giá trị được gán cho 'password' khi lưu vào DB
        // và đảm bảo giá trị hash không bao giờ bị trả về khi truy cập thuộc tính này
            'password' => 'hashed',
        // Ép kiểu 'birthday' thành đối tượng Carbon (chỉ ngày) khi truy cập
        'birthday' => 'date',
        'deleted_at' => 'datetime'
    ];

    /**
     * Accessor (Getter) tùy chỉnh để lấy URL đầy đủ của avatar người dùng.
     * Phương thức này sẽ được gọi tự động khi bạn truy cập thuộc tính ảo `avatar_url` (ví dụ: `$user->avatar_url`).
     * Tên phương thức phải theo quy ước: get{TênThuộcTínhCamelCase}Attribute.
     *
     * @return string URL của avatar (có thể là avatar thật hoặc avatar mặc định)
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            return Storage::url($this->avatar);
        }
        return asset('stickers/avatar_icon.png');
    }

    /**
     * Định nghĩa quan hệ một-nhiều: Một người dùng (User) có thể có nhiều bài viết (Post).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        // `hasMany` định nghĩa quan hệ một-nhiều.
        // Tham số thứ nhất: Tên lớp Model liên quan (Post).
        // Laravel sẽ tự động tìm khóa ngoại `user_id` trong bảng `posts`.
        return $this->hasMany(Post::class);
    }

    /**
     * Định nghĩa quan hệ một-nhiều: Một người dùng (User) có thể gửi nhiều tin nhắn 1-1 (Message).
     * Liên kết qua khóa ngoại 'sender_id' trong bảng 'messages'.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sentMessages()
    {
        // Tham số thứ hai ('sender_id') chỉ định tên cột khóa ngoại trong bảng 'messages' liên kết đến 'id' của User này.
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Định nghĩa quan hệ một-nhiều: Một người dùng (User) có thể nhận nhiều tin nhắn 1-1 (Message).
     * Liên kết qua khóa ngoại 'receiver_id' trong bảng 'messages'.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function receivedMessages()
    {
        // Tham số thứ hai ('receiver_id') chỉ định tên cột khóa ngoại trong bảng 'messages' liên kết đến 'id' của User này.
        return $this->hasMany(Message::class, 'receiver_id');
    }

    /**
     * Định nghĩa quan hệ để lấy các tin nhắn chưa đọc mà người dùng này đã nhận.
     * Thực chất là một cách lọc dựa trên quan hệ `receivedMessages`.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function unreadMessages()
    {
        // Bắt đầu từ quan hệ `receivedMessages` và thêm điều kiện lọc `where`
        return $this->receivedMessages()->where('is_read', false); // Chỉ lấy tin nhắn có is_read = false
    }

    /**
     * Phương thức helper để đếm số lượng tin nhắn chưa đọc từ một người gửi cụ thể.
     *
     * @param int $senderId ID của người gửi cần kiểm tra
     * @return int Số lượng tin nhắn chưa đọc từ người gửi đó
     */
    public function getUnreadMessagesFrom($senderId)
    {
        // Bắt đầu từ quan hệ `receivedMessages` (tin nhắn user này nhận)
        return $this->receivedMessages()
            ->where('sender_id', $senderId) // Lọc theo ID người gửi
            ->where('is_read', false)      // Chỉ lấy tin nhắn chưa đọc
            ->count();                    // Đếm số lượng kết quả
    }

    /**
     * Phương thức helper để lấy tin nhắn cuối cùng (gửi hoặc nhận) giữa người dùng này và một người dùng khác.
     *
     * @param int $userId ID của người dùng khác
     * @return \App\Models\Message|null Trả về đối tượng Message cuối cùng, hoặc null nếu không có tin nhắn nào.
     */
    public function getLastMessageWith($userId)
    {
        // Truy vấn trực tiếp model Message
        return Message::where(function ($query) use ($userId) {
            // Điều kiện: (người gửi là tôi VÀ người nhận là $userId)
            $query->where('sender_id', $this->id)
                ->where('receiver_id', $userId);
        })->orWhere(function ($query) use ($userId) { // HOẶC
            // Điều kiện: (người gửi là $userId VÀ người nhận là tôi)
            $query->where('sender_id', $userId)
                ->where('receiver_id', $this->id);
        })
            ->latest() // Sắp xếp theo thời gian tạo giảm dần (lấy tin mới nhất)
            ->first(); // Chỉ lấy bản ghi đầu tiên (tin nhắn mới nhất)
    }

    /**
     * Định nghĩa quan hệ nhiều-nhiều: Một người dùng (User) có thể tham gia nhiều nhóm chat (ChatGroup).
     * Liên kết thông qua bảng trung gian (pivot table) 'chat_group_members'.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function chatGroups()
    {
        // `belongsToMany` xác định quan hệ nhiều-nhiều.
        // Tham số thứ nhất: Tên lớp Model liên quan (ChatGroup).
        // Tham số thứ hai: Tên bảng trung gian.
        // Tham số thứ ba: Tên khóa ngoại trong bảng trung gian liên kết đến model hiện tại (User).
        // Tham số thứ tư: Tên khóa ngoại trong bảng trung gian liên kết đến model liên quan (ChatGroup).
        return $this->belongsToMany(ChatGroup::class, 'chat_group_members', 'user_id', 'group_id')
            // `withPivot` cho phép truy cập các cột bổ sung trong bảng trung gian khi lấy dữ liệu quan hệ.
            ->withPivot('is_admin_group_chat') // Thay đổi từ is_admin thành is_admin_group_chat
            // `withTimestamps` tự động quản lý cột 'created_at' và 'updated_at' trong bảng pivot khi attach/detach.
            ->withTimestamps();
    }

    /**
     * Accessor (Getter) tùy chỉnh để lấy danh sách các nhóm chat mà người dùng này là thành viên.
     * Kèm theo thông tin thành viên và tin nhắn cuối cùng của mỗi nhóm để hiển thị tóm tắt.
     * Sẽ được gọi khi bạn truy cập thuộc tính ảo `$user->groups`.
     *
     * @return \Illuminate\Database\Eloquent\Collection Collection các đối tượng ChatGroup
     */
    public function getGroupsAttribute() // Tên phương thức: get{TênThuộcTínhCamelCase}Attribute
    {
        // Bắt đầu từ quan hệ `chatGroups` đã định nghĩa ở trên
        return $this->chatGroups()
            // Eager load (tải trước) các relationship của mỗi ChatGroup để tối ưu truy vấn
            ->with([
                'members', // Tải tất cả thành viên của nhóm
                'messages' => function ($query) { // Tải tin nhắn, nhưng tùy chỉnh query
                    $query->latest() // Sắp xếp tin nhắn mới nhất lên đầu
                        ->take(1); // Chỉ lấy 1 tin nhắn (tin nhắn mới nhất)
                }
            ])
            ->get(); // Lấy kết quả là một Collection các ChatGroup
    }

    /**
     * Get the groups created by the user.
     */
    public function groups()
    {
        return $this->hasMany(Group::class, 'created_by');
    }

    /**
     * Get the groups that the user is a member of.
     */
    public function joinedGroups()
    {
        return $this->belongsToMany(Group::class, 'group_members')
            ->withPivot('role', 'is_approved')
            ->withTimestamps();
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers', 'following_id', 'follower_id');
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'following_id');
    }

    public function isFollowing($user)
    {
        return $this->following()->where('users.id', $user->id)->exists();
    }

    /**
     * Định nghĩa quan hệ với những người dùng đã bị chặn bởi người dùng này
     */
    public function blockedUsers()
    {
        return $this->belongsToMany(User::class, 'user_blocks', 'blocker_id', 'blocked_id')
                    ->withTimestamps();
    }

    /**
     * Định nghĩa quan hệ với những người dùng đã chặn người dùng này
     */
    public function blockedByUsers()
    {
        return $this->belongsToMany(User::class, 'user_blocks', 'blocked_id', 'blocker_id')
                    ->withTimestamps();
    }

    /**
     * Kiểm tra xem người dùng hiện tại có chặn một người dùng khác không
     */
    public function hasBlocked($userId)
    {
        return $this->blockedUsers()->where('blocked_id', $userId)->exists();
    }

    /**
     * Chặn một người dùng
     */
    public function block($userId)
    {
        if (!$this->hasBlocked($userId)) {
            $this->blockedUsers()->attach($userId);
        }
    }

    /**
     * Bỏ chặn một người dùng
     */
    public function unblock($userId)
    {
        $this->blockedUsers()->detach($userId);
    }

    /**
     * Định nghĩa quan hệ với những người dùng đã báo cáo người dùng này (reportedByUsers)
     * (Ai đã báo cáo tôi)
     */
    public function reportedByUsers()
    {
        // Bảng pivot: user_user_reports
        // Khóa ngoại của model hiện tại (người bị báo cáo): reported_user_id
        // Khóa ngoại của model liên quan (người báo cáo): reporter_id
        return $this->belongsToMany(User::class, 'user_user_reports', 'reported_user_id', 'reporter_id')
                    ->withPivot('reason', 'is_resolved', 'admin_note')
                    ->withTimestamps();
    }

    /**
     * Định nghĩa quan hệ với những người dùng mà người dùng này đã báo cáo (reportedUsers)
     * (Ai được tôi báo cáo)
     */
    public function reportedUsers()
    {
        // Bảng pivot: user_user_reports
        // Khóa ngoại của model hiện tại (người báo cáo): reporter_id
        // Khóa ngoại của model liên quan (người bị báo cáo): reported_user_id
        return $this->belongsToMany(User::class, 'user_user_reports', 'reporter_id', 'reported_user_id')
                    ->withPivot('reason', 'is_resolved', 'admin_note')
                    ->withTimestamps();
    }

    /**
     * Kiểm tra xem người dùng hiện tại đã báo cáo một người dùng khác chưa
     */
    public function hasReported($userId)
    {
        // Sử dụng quan hệ reportedUsers và kiểm tra reported_user_id
        return $this->reportedUsers()->where('reported_user_id', $userId)->exists();
    }

    /**
     * Báo cáo một người dùng
     *
     * @param int $userId ID của người dùng bị báo cáo
     * @param string $reason Lý do báo cáo
     * @return bool True nếu báo cáo mới được tạo, false nếu đã tồn tại
     */
    public function report($userId, $reason)
    {
        // Kiểm tra xem người dùng đã báo cáo người dùng này chưa
        if (!$this->hasReported($userId)) {
            // Sử dụng quan hệ reportedUsers để tạo bản ghi trong bảng pivot user_user_reports
            $this->reportedUsers()->attach($userId, [
                'reason' => $reason,
                'is_resolved' => false // Cột này tồn tại trong bảng user_user_reports mới
            ]);
             return true; // Báo cáo mới được tạo
        }
        return false; // Đã báo cáo trước đó
    }

    /**
     * Hủy báo cáo một người dùng
     */
    public function unreport($userId)
    {
        // Sử dụng quan hệ reportedUsers để xóa bản ghi trong bảng pivot
        $this->reportedUsers()->detach($userId);
    }

    /**
     * Scope để loại bỏ những người dùng đã bị chặn khỏi kết quả tìm kiếm
     */
    public function scopeExcludeBlocked($query)
    {
        return $query->whereNotIn('id', function($subquery) {
            $subquery->select('blocked_id')
                    ->from('user_blocks')
                    ->where('blocker_id', auth()->id());
        });
    }

    /**
     * Định nghĩa quan hệ với các bài viết mà người dùng này đã yêu thích.
     */
    public function favoritedPosts()
    {
        return $this->belongsToMany(Post::class, 'post_favorites', 'user_id', 'post_id')->withTimestamps();
    }

    /**
     * Kiểm tra xem tài khoản có đang hoạt động không
     */
    public function isActive()
    {
        return $this->account_status === 'active';
    }

    /**
     * Kiểm tra xem tài khoản có bị vô hiệu hóa không
     */
    public function isDisabled()
    {
        return $this->account_status === 'disabled';
    }

    /**
     * Kiểm tra xem tài khoản có bị xóa không
     */
    public function isDeleted()
    {
        return $this->account_status === 'deleted';
    }

    /**
     * Vô hiệu hóa tài khoản
     */
    public function disable()
    {
        $this->update([
            'account_status' => 'disabled',
            'deleted_at' => now()
        ]);
    }

    /**
     * Xóa tài khoản vĩnh viễn
     */
    public function deletePermanently()
    {
        // Xóa avatar nếu có
        if ($this->avatar) {
            Storage::disk('public')->delete($this->avatar);
        }

        // Xóa tất cả các bài viết của người dùng
        $this->posts()->delete();

        // Xóa tất cả các tin nhắn của người dùng
        $this->sentMessages()->delete();
        $this->receivedMessages()->delete();

        // Xóa tất cả các báo cáo liên quan
        $this->reportedByUsers()->detach();
        $this->reportedUsers()->detach();

        // Xóa tất cả các mối quan hệ chặn
        $this->blockedUsers()->detach();
        $this->blockedByUsers()->detach();

        // Xóa tất cả các mối quan hệ theo dõi
        $this->followers()->detach();
        $this->following()->detach();

        // Xóa tất cả các bài viết yêu thích
        $this->favoritedPosts()->detach();

        // Cập nhật trạng thái tài khoản thành đã xóa
        $this->update([
            'account_status' => 'deleted',
            'deleted_at' => now()
        ]);
    }

    /**
     * Khôi phục tài khoản
     */
    public function restore()
    {
        $this->update([
            'account_status' => 'active',
            'deleted_at' => null
        ]);
    }

    /**
     * Lấy danh sách bạn bè
     */
    public function friends()
    {
        return $this->belongsToMany(User::class, 'friends', 'user_id', 'friend_id')
            ->withPivot('status')
            ->withTimestamps();
    }

    /**
     * Kiểm tra xem người dùng hiện tại có phải là bạn với người dùng khác không
     */
    public function isFriendWith(User $user)
    {
        return $this->friends()
            ->where('friend_id', $user->id)
            ->where('status', 'accepted')
            ->exists();
    }

    /**
     * Kiểm tra xem người dùng hiện tại đã gửi lời mời kết bạn cho người dùng khác chưa
     */
    public function hasSentFriendRequestTo(User $user)
    {
        return $this->friends()
            ->where('friend_id', $user->id)
            ->where('status', 'pending')
            ->exists();
    }

    /**
     * Kiểm tra xem người dùng hiện tại đã nhận lời mời kết bạn từ người dùng khác chưa
     */
    public function hasReceivedFriendRequestFrom(User $user)
    {
        return DB::table('friends')
            ->where('user_id', $user->id)
            ->where('friend_id', $this->id)
            ->where('status', 'pending')
            ->exists();
    }

    /**
     * Lấy danh sách bạn bè đã chấp nhận
     */
    public function acceptedFriends()
    {
        return $this->belongsToMany(User::class, 'friends', 'user_id', 'friend_id')
            ->wherePivot('status', 'accepted')
            ->withTimestamps();
    }

    /**
     * Lấy danh sách lời mời kết bạn đang chờ
     */
    public function pendingFriendRequests()
    {
        return $this->belongsToMany(User::class, 'friends', 'friend_id', 'user_id')
            ->wherePivot('status', 'pending')
            ->withTimestamps();
    }

    /**
     * Lấy danh sách lời mời kết bạn đã gửi
     */
    public function sentFriendRequests()
    {
        return $this->belongsToMany(User::class, 'friends', 'user_id', 'friend_id')
            ->wherePivot('status', 'pending')
            ->withTimestamps();
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function joinedEvents()
    {
        return $this->belongsToMany(Event::class)
                    ->wherePivot('status', 'joined')
                    ->withPivot('joined_at')
                    ->withTimestamps();
    }

    public function getParticipatedEventsAttribute()
    {
        return $this->belongsToMany(Event::class)
                    ->wherePivot('status', 'joined')
                    ->withPivot('joined_at')
                    ->withTimestamps();
    }

    public function notifications()
    {
        return $this->hasMany(\App\Models\Notification::class, 'user_id');
    }
}
