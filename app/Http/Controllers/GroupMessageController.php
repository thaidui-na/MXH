<?php

namespace App\Http\Controllers;

use App\Models\ChatGroup;
use App\Models\GroupMessage;
use Illuminate\Http\Request;

/**
 * Controller xử lý các chức năng liên quan đến tin nhắn trong nhóm chat
 * Bao gồm: gửi tin nhắn, kiểm tra tin nhắn mới
 */
class GroupMessageController extends Controller
{
    /**
     * Lưu tin nhắn mới vào nhóm chat.
     *
     * @param  \Illuminate\Http\Request  $request Dữ liệu gửi lên từ client
     * @param  \App\Models\ChatGroup  $group Nhóm chat mà tin nhắn thuộc về (được inject tự động)
     * @return \Illuminate\Http\JsonResponse Phản hồi JSON cho client (thường là AJAX)
     */
    public function store(Request $request, ChatGroup $group)
    {
        // Ghi log thông tin request nhận được để debug
        \Log::info('Receiving message request', $request->all());

        // Kiểm tra xem người dùng hiện tại có phải là thành viên của nhóm chat này không
        if (!$group->members()->where('user_id', auth()->id())->exists()) {
            // Nếu không phải thành viên, trả về lỗi 403 (Forbidden)
            return response()->json([
                'success' => false,
                'error' => 'Bạn không phải là thành viên của nhóm này'
            ], 403);
        }

        // Validate dữ liệu gửi lên, yêu cầu phải có trường 'content' và là chuỗi
        $request->validate([
            'content' => 'required|string'
        ]);

        // Sử dụng try-catch để bắt lỗi trong quá trình xử lý
        try {
            // Tạo một bản ghi tin nhắn mới trong database
            $message = GroupMessage::create([
                'sender_id' => auth()->id(), // ID của người gửi (người dùng đang đăng nhập)
                'group_id' => $group->id, // ID của nhóm chat
                'content' => $request->content // Nội dung tin nhắn từ request
            ]);

            // Tải thông tin của người gửi (sender) cho tin nhắn vừa tạo
            // Điều này hữu ích để hiển thị tên hoặc avatar người gửi mà không cần query lại
            $message->load('sender');

            // Ghi log xác nhận tin nhắn đã được tạo thành công
            \Log::info('Message created successfully', ['message_id' => $message->id]);

            // Render HTML cho tin nhắn mới sử dụng partial view 'chat_groups.partials.message'
            // Truyền biến 'message' chứa thông tin tin nhắn vừa tạo vào view
            $messageHtml = view('chat_groups.partials.message', ['message' => $message])->render();

            // Trả về phản hồi JSON thành công cho client
            return response()->json([
                'success' => true, // Trạng thái thành công
                'messageHtml' => $messageHtml, // HTML của tin nhắn để hiển thị trên giao diện
                'message' => $message // Dữ liệu tin nhắn đầy đủ (bao gồm cả sender)
            ]);

        } catch (\Exception $e) {
            // Nếu có lỗi xảy ra trong khối try, ghi log chi tiết lỗi
            \Log::error('Error creating group message: ' . $e->getMessage());
            // Trả về phản hồi JSON báo lỗi cho client
            return response()->json([
                'success' => false, // Trạng thái thất bại
                'error' => 'Có lỗi xảy ra khi gửi tin nhắn: ' . $e->getMessage() // Thông báo lỗi
            ], 500); // Mã lỗi 500 (Internal Server Error)
        }
    }

    /**
     * Kiểm tra và lấy các tin nhắn mới trong nhóm kể từ một ID cụ thể.
     * Thường được gọi định kỳ bằng AJAX để cập nhật giao diện chat real-time.
     *
     * @param  \Illuminate\Http\Request  $request Request chứa ID của tin nhắn cuối cùng đã hiển thị ('last_id')
     * @param  \App\Models\ChatGroup  $group Nhóm chat cần kiểm tra tin nhắn mới
     * @return \Illuminate\Http\JsonResponse Phản hồi JSON chứa các tin nhắn mới (dưới dạng HTML) hoặc báo lỗi
     */
    public function checkNewMessages(Request $request, ChatGroup $group)
    {
        // Kiểm tra xem người dùng hiện tại có phải là thành viên của nhóm chat này không
        if (!$group->members()->where('user_id', auth()->id())->exists()) {
             // Nếu không phải thành viên, trả về lỗi 403 (Forbidden)
            return response()->json([
                'success' => false,
                'error' => 'Bạn không phải là thành viên của nhóm này'
            ], 403);
        }

        // Sử dụng try-catch để bắt lỗi trong quá trình xử lý
        try {
            // Lấy giá trị 'last_id' từ query string của request
            // Nếu không có 'last_id', mặc định là 0 (để lấy tất cả tin nhắn ban đầu nếu cần)
            $lastId = $request->query('last_id', 0);
            
            // Truy vấn database để lấy các tin nhắn trong nhóm có ID lớn hơn 'lastId'
            $newMessages = $group->messages() // Lấy relationship 'messages' của nhóm
                ->where('id', '>', $lastId) // Điều kiện lọc tin nhắn mới
                ->with('sender') // Tải kèm thông tin người gửi để tránh N+1 query
                ->orderBy('created_at', 'asc') // Sắp xếp theo thời gian tạo tăng dần
                ->get(); // Thực thi query và lấy kết quả

            // Nếu không có tin nhắn mới nào được tìm thấy
            if ($newMessages->isEmpty()) {
                 // Trả về phản hồi JSON thành công với mảng messages rỗng
                return response()->json([
                    'success' => true,
                    'messages' => [] // Không có tin nhắn mới
                ]);
            }

            // Nếu có tin nhắn mới, render HTML cho từng tin nhắn
            // Sử dụng phương thức map() của Collection để duyệt qua từng tin nhắn
            $messagesHtml = $newMessages->map(function($message) {
                // Render partial view 'chat_groups.partials.message' cho mỗi tin nhắn
                return view('chat_groups.partials.message', ['message' => $message])->render();
            }); // Kết quả là một Collection chứa các chuỗi HTML

            // Trả về phản hồi JSON thành công
            return response()->json([
                'success' => true, // Trạng thái thành công
                'messages' => $messagesHtml, // Mảng/Collection chứa HTML của các tin nhắn mới
                'lastMessageId' => $newMessages->last()->id // ID của tin nhắn mới nhất trong loạt này
            ]);

        } catch (\Exception $e) {
             // Nếu có lỗi xảy ra trong khối try, ghi log chi tiết lỗi
            \Log::error('Error checking new messages: ' . $e->getMessage());
             // Trả về phản hồi JSON báo lỗi cho client
            return response()->json([
                'success' => false, // Trạng thái thất bại
                'error' => 'Có lỗi xảy ra khi kiểm tra tin nhắn mới' // Thông báo lỗi chung
            ], 500); // Mã lỗi 500 (Internal Server Error)
        }
    }
}
