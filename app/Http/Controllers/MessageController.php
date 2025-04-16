<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Controller quản lý các chức năng liên quan đến tin nhắn 1-1 giữa người dùng
 * Bao gồm: hiển thị danh sách chat, hiển thị cuộc trò chuyện, gửi tin nhắn, lấy tin nhắn mới
 */
class MessageController extends Controller
{
    /**
     * Hiển thị trang tin nhắn chính.
     * Lấy danh sách người dùng để hiển thị bên sidebar.
     * Hiển thị cuộc trò chuyện với người dùng đầu tiên trong danh sách (nếu có).
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Lấy danh sách tất cả người dùng trừ người dùng đang đăng nhập
        $users = User::where('id', '!=', auth()->id())->get();

        // Chọn người dùng đầu tiên trong danh sách để hiển thị cuộc trò chuyện mặc định
        $selectedUser = $users->first();
        // Khởi tạo mảng tin nhắn rỗng
        $messages = [];
        
        // Nếu có người dùng trong danh sách
        if ($selectedUser) {
            // Lấy lịch sử tin nhắn với người dùng được chọn
            $messages = $this->getMessagesWith($selectedUser->id);
        }

        // Trả về view 'messages.index' và truyền các biến cần thiết vào view
        return view('messages.index', compact('users', 'selectedUser', 'messages'));
    }

    /**
     * Hiển thị cuộc trò chuyện với một người dùng cụ thể.
     * Lấy thông tin người dùng và lịch sử tin nhắn.
     * Đánh dấu các tin nhắn chưa đọc từ người dùng đó là đã đọc.
     * Có thể trả về JSON nếu là request AJAX (để cập nhật động).
     *
     * @param  int  $userId ID của người dùng muốn xem cuộc trò chuyện
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function show($userId) // Laravel tự động inject ID từ route
    {
        // Tìm người dùng được chọn bằng ID, nếu không tìm thấy sẽ ném lỗi ModelNotFoundException
        $selectedUser = User::findOrFail($userId);
        // Lấy lịch sử tin nhắn với người dùng này
        $messages = $this->getMessagesWith($userId);

        // Đánh dấu tất cả tin nhắn nhận được từ người dùng này mà chưa đọc thành đã đọc
        auth()->user()->receivedMessages() // Lấy các tin nhắn mà user hiện tại là người nhận
            ->where('sender_id', $userId) // Lọc theo người gửi là người dùng đang xem
            ->where('is_read', false) // Chỉ lấy các tin nhắn chưa đọc
            ->update(['is_read' => true]); // Cập nhật trạng thái is_read thành true

        // Kiểm tra nếu request là AJAX
        if (request()->ajax()) {
            // Trả về JSON chứa HTML của danh sách tin nhắn và thông tin người dùng được chọn
            return response()->json([
                // Render partial view 'messages.partials.message-list' với dữ liệu tin nhắn và người dùng
                'messages' => view('messages.partials.message-list', compact('messages', 'selectedUser'))->render(),
                'user' => $selectedUser // Thông tin người dùng được chọn
            ]);
        }

        // Nếu không phải AJAX, lấy lại danh sách người dùng để hiển thị sidebar
        $users = User::where('id', '!=', auth()->id())->get();
        // Trả về view 'messages.index' và truyền các biến cần thiết
        return view('messages.index', compact('users', 'selectedUser', 'messages'));
    }

    /**
     * Lưu tin nhắn mới vào database.
     * Xử lý nội dung text, hình ảnh, sticker, emoji.
     * Validate dữ liệu đầu vào.
     * Thường được gọi qua AJAX.
     *
     * @param  \Illuminate\Http\Request  $request Dữ liệu tin nhắn gửi lên từ client
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            // Validate dữ liệu
            $request->validate([
                'receiver_id' => 'required|exists:users,id',
                'content' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'sticker' => 'nullable|string',
                'emoji' => 'nullable|string'
            ]);

            // Bắt đầu transaction
            DB::beginTransaction();

            // Chuẩn bị dữ liệu tin nhắn
            $messageData = [
                'sender_id' => auth()->id(),
                'receiver_id' => $request->receiver_id,
                'content' => $request->content,
                'sticker' => $request->sticker,
                'emoji' => $request->emoji,
                'is_read' => false
            ];

            // Xử lý upload hình ảnh
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('message-images', 'public');
                $messageData['image_path'] = $path;
            }

            // Kiểm tra có nội dung gì không
            if (empty($messageData['content']) && 
                empty($messageData['image_path']) && 
                empty($messageData['sticker']) && 
                empty($messageData['emoji'])) {
                return response()->json([
                    'error' => 'Vui lòng nhập nội dung tin nhắn hoặc chọn hình ảnh/sticker'
                ], 422);
            }

            // Tạo tin nhắn
            $message = Message::create($messageData);

            // Commit transaction
            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'message' => view('messages.partials.single-message', ['message' => $message])->render()
                ]);
            }

            return back();

        } catch (\Exception $e) {
            // Rollback nếu có lỗi
            DB::rollBack();
            
            return response()->json([
                'error' => 'Có lỗi xảy ra khi gửi tin nhắn: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Phương thức private helper để lấy lịch sử tin nhắn giữa người dùng hiện tại và một người dùng khác.
     * Lấy cả tin nhắn gửi đi và nhận về.
     * Sắp xếp theo thời gian tạo.
     *
     * @param  int  $userId ID của người dùng muốn lấy lịch sử chat cùng
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getMessagesWith($userId)
    {
        // Truy vấn bảng 'messages'
        return Message::where(function($query) use ($userId) {
            // Điều kiện 1: tin nhắn gửi từ user hiện tại ĐẾN userId
            $query->where('sender_id', auth()->id())
                  ->where('receiver_id', $userId);
        })->orWhere(function($query) use ($userId) { // Hoặc
            // Điều kiện 2: tin nhắn gửi từ userId ĐẾN user hiện tại
            $query->where('sender_id', $userId)
                  ->where('receiver_id', auth()->id());
        })
        ->orderBy('created_at', 'asc') // Sắp xếp theo thời gian tạo tăng dần (từ cũ đến mới)
        ->get(); // Lấy tất cả các tin nhắn thỏa mãn điều kiện
    }

    /**
     * Lấy các tin nhắn mới nhận được từ một người dùng cụ thể mà chưa được đọc.
     * Được sử dụng cho cơ chế polling bằng AJAX để cập nhật real-time.
     * Đánh dấu các tin nhắn này là đã đọc sau khi lấy.
     *
     * @param  int  $userId ID của người dùng gửi tin nhắn mới
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNewMessages($userId)
    {
        // Truy vấn các tin nhắn: gửi từ userId, nhận bởi user hiện tại, và chưa đọc
        $messages = Message::where('sender_id', $userId)
            ->where('receiver_id', auth()->id())
            ->where('is_read', false)
            ->get();

        // Đánh dấu các tin nhắn vừa lấy là đã đọc
        foreach ($messages as $message) {
            // Cập nhật trường 'is_read' thành true cho từng tin nhắn
            $message->update(['is_read' => true]);
        }

        // Trả về JSON chứa HTML của danh sách tin nhắn mới
        return response()->json([
            // Render partial view 'messages.partials.message-list' với các tin nhắn mới
            // Lưu ý: view này cần được thiết kế để chỉ hiển thị các tin nhắn được truyền vào
            'messages' => view('messages.partials.message-list', ['messages' => $messages])->render()
        ]);
    }

    /**
     * Lấy trạng thái (số tin nhắn chưa đọc, tin nhắn cuối cùng) của tất cả người dùng khác.
     * Được sử dụng để cập nhật trạng thái trên sidebar danh sách người dùng bằng AJAX.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsersStatus()
    {
        // Lấy tất cả người dùng trừ người dùng hiện tại
        $users = User::where('id', '!=', auth()->id())
            ->get()
            // Sử dụng map để biến đổi collection, tính toán trạng thái cho mỗi user
            ->map(function($user) {
                // Trả về một mảng chứa thông tin trạng thái cho mỗi user
                return [
                    'id' => $user->id, // ID của người dùng
                    // Lấy số lượng tin nhắn chưa đọc từ người dùng này gửi đến user hiện tại
                    // Sử dụng phương thức helper getUnreadMessagesFrom() trong User model (cần được định nghĩa)
                    'unread_count' => auth()->user()->getUnreadMessagesFrom($user->id),
                    // Lấy nội dung hoặc mô tả của tin nhắn cuối cùng giữa hai người
                    // Sử dụng phương thức helper getLastMessageWith() trong User model (cần được định nghĩa)
                    'last_message' => $user->getLastMessageWith(auth()->id())
                ];
            });

        // Trả về JSON chứa mảng thông tin trạng thái của các người dùng
        return response()->json(['users' => $users]);
    }
} 