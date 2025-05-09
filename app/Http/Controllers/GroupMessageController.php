<?php

namespace App\Http\Controllers;

use App\Models\ChatGroup;
use App\Models\GroupMessage;
use Illuminate\Http\Request;

class GroupMessageController extends Controller
{
    public function store(Request $request, ChatGroup $group)
    {
        \Log::info('Receiving message request', $request->all());

        // Kiểm tra thành viên nhóm
        if (!$group->members()->where('user_id', auth()->id())->exists()) {
            return response()->json([
                'success' => false,
                'error' => 'Bạn không phải là thành viên của nhóm này'
            ], 403);
        }

        // Validate dữ liệu
        $request->validate([
            'content' => 'required|string'
        ]);

        try {
            // Tạo tin nhắn mới
            $message = GroupMessage::create([
                'sender_id' => auth()->id(),
                'group_id' => $group->id,
                'content' => $request->content
            ]);

            $message->load('sender');

            \Log::info('Message created successfully', ['message_id' => $message->id]);

            // Render message partial view
            $messageHtml = view('chat_groups.partials.message', ['message' => $message])->render();

            return response()->json([
                'success' => true,
                'messageHtml' => $messageHtml,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            \Log::error('Error creating group message: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Có lỗi xảy ra khi gửi tin nhắn: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kiểm tra tin nhắn mới
     */
    public function checkNewMessages(Request $request, ChatGroup $group)
    {
        // Kiểm tra thành viên nhóm
        if (!$group->members()->where('user_id', auth()->id())->exists()) {
            return response()->json([
                'success' => false,
                'error' => 'Bạn không phải là thành viên của nhóm này'
            ], 403);
        }

        try {
            // Lấy tin nhắn mới từ ID cuối cùng
            $lastId = $request->query('last_id', 0);
            
            $newMessages = $group->messages()
                ->where('id', '>', $lastId)
                ->with('sender')
                ->orderBy('created_at', 'asc')
                ->get();

            if ($newMessages->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'messages' => []
                ]);
            }

            // Render từng tin nhắn mới
            $messagesHtml = $newMessages->map(function($message) {
                return view('chat_groups.partials.message', ['message' => $message])->render();
            });

            return response()->json([
                'success' => true,
                'messages' => $messagesHtml,
                'lastMessageId' => $newMessages->last()->id
            ]);

        } catch (\Exception $e) {
            \Log::error('Error checking new messages: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Có lỗi xảy ra khi kiểm tra tin nhắn mới'
            ], 500);
        }
    }
}
